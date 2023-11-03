<?php namespace Modules\Person;

use Defines\Content\Attribute;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    public function toCheck()
    {
        $oHelper = new \Data\ContentHelper();
        $oManager = $oHelper->getEntityManager();
        $sUrl = 'person/' . \System\Registry::user()->getName();
        // Remove previous version
        $aOld = $oHelper->findByUrl($sUrl, \Defines\Database\CrMain::CONTENT);
        $aFiles = array();
        foreach ($aOld as $o) {
            $s = $o->getContent();
            if (strpos($s, '/data/') === 0) {
                $aFiles[$s] = \System\Registry::config()->getPublicPath() . $s;
            }
            // remove entities
            $oManager->remove($o);
        }
        // Send to check new data
        $a = $oHelper->findByUrl($sUrl, \Defines\Database\CrMain::CONTENT_NEW);
        /* @var $o \Data\Doctrine\Main\ContentNew */
        foreach ($a as $o) {
            $o->setAuditor(null)
                ->setAccess(\Defines\User\Access::getAudit());
            if (strpos($o->getContent(), '/data/') === 0) {
                unset($aFiles[$o->getContent()]);
            }
            $oManager->persist($o);
        }

        // remove files
        foreach ($aFiles as $sFile) {
            if (file_exists($sFile)) {
                unlink($sFile);
            }
        }

        $oManager->flush();
    }

    public function savePersonal($aNew)
    {
        $oUser = \System\Registry::user()->getEntity();
        $sLang = \System\Registry::translation()->getTargetLanguage();

        $oHelper = new \Data\ContentHelper();
        $oManager = $oHelper->getEntityManager();
        $oManager->getConnection()->beginTransaction();
        try {
            // Delete old data
            $sUrl = 'person/' . \System\Registry::user()->getName();
            $aOld = $oHelper->findByUrl($sUrl, \Defines\Database\CrMain::CONTENT_NEW);
            /* @var $o \Data\Doctrine\Main\ContentNew */
            foreach ($aOld as $o) {
                // Delete old blob
                if (!isset($aNew[$o->getType()]) || strpos($aNew[$o->getType()], 'image/png') !== false) {
                    $oHelper->delTmpBlob($oUser, $sLang, $o->getPattern(), $o->getType());
                }
                $oManager->remove($o);
            }
            $oManager->flush();

            // Add new data
            foreach ($aNew as $sType => $sData) {
                if (strpos($sData, 'image/png') !== false) {
                    $id = $oHelper->saveBlob($sUrl, $sType, $sData);
                    $sData = "/files/$id";
                }

                $oContent = new \System\Converter\Content($sData);

                $o = new \Data\Doctrine\Main\ContentNew();
                $o->setAuthor($oUser)
                    ->setAuditor($oUser)
                    ->setType($sType)
                    ->setContent($oContent->getHtml())
                    ->setPattern($sUrl)
                    ->setLanguage($sLang)
                    ->setUpdatedAt(new \DateTime())
                    ->setAccess(\Defines\User\Access::getNew());
                $oManager->persist($o);
            }

            $oManager->flush();
            $oManager->getConnection()->commit();
        } catch (\Exception $e) {
            $oManager->getConnection()->rollBack();
            throw new \Error\Validation('Saving process has been failed');
        }
    }

    /**
     * Display user information
     *
     * @param string $sUser - username
     * @param boolean $bNew - display newest user data
     * @return array
     * @throws \Error\Validation
     */
    public function getPersonal($sUser = null, $bNew = false)
    {
        $oHelper = new \Data\ContentHelper();
        if (is_null($sUser)) {
            $sUser = \System\Registry::user()->getName();
            $bNew = true;
        }

        $oUserHelper = new \Data\UserHelper();
        /* @var $oUser \Data\Doctrine\Main\User */
        $oUser = $oUserHelper->getUserByName($sUser);
        if (!$oUser) {
            $oUser = $oUserHelper->getUserByName($sUser, false);
            if (!$oUser) {
                throw new \Error\Validation(\System\Registry::translation()->sys('LB_ERROR_USER_MISSING'));
            } else {
                (new \Deprecated\Migration)->redirect(\System\Registry::config()->getUrl() . '/person/' . $oUser->getUsername() . '.html');
            }
        }

        $sUrl = "person/$sUser";
        // Check public information
        $aData = $oHelper->findByUrl($sUrl);
        $broken = false;
        foreach ($aData as $o) {
            if (strpos($o->getContent(), '{') === 0) {
                $broken = true;
            }
        }
        // If a user information wasn't yet created
        if (!$aData || $broken) {
            $tmp = new \System\ArrayUndef([]);
            $tmp->setUndefined(function() {
                $o = new \Data\Doctrine\Main\Content();
                $o->setUpdatedAt(new \DateTime);
                return $o;
            });
            /* @var $oContent \Data\Doctrine\Main\Content */
            foreach ($aData as $oContent) {
                $tmp[$oContent->getType()] = $oContent;
            }
            $aData = $this->autocreateInfo($sUser, $tmp);
            // Check private changes
        } elseif ($bNew) {
            $aNewData = $oHelper->findByUrl($sUrl, \Defines\Database\CrMain::CONTENT_NEW);
            if ($aNewData) {
                $aData = $aNewData;
                $bNew = true;
            }
        }

        // Get languages list
        $aLanguages = array();
        $tmp = $oHelper->getRepository()->findBy(array(
            'pattern' => $sUrl,
            'type' => 'og:title'
        ));
        /* @var $o \Data\Doctrine\Main\Content */
        foreach ($tmp as $o) {
            $aLanguages[] = $o->getLanguage();
        }

        // Fill content
        $aContent = array();
        /* @var $oContent \Data\Doctrine\Main\Content */
        foreach ($aData as $oContent) {
            $aContent[$oContent->getType()] = $oContent;
        }

        return array(
            'languages' => $aLanguages,
            'content' => $aContent,
            'user' => $oUser,
            'username' => $oUser->getUsername(),
            'url' => $sUrl,
            'access' => $oUserHelper->getUserPrivileges($oUser),
            'accounts' => $oUserHelper->getUserAccounts($oUser),
            'new' => $bNew
        );
    }

    /**
     * Autocreate user information
     *
     * @param string $sUser
     * @param \System\ArrayUndef $aData
     * @return array
     */
    public function autocreateInfo($sUser, $aData, $sType = 'search')
    {
        $em = \System\Registry::connection();
        $oTranslate = \System\Registry::translation();
        $aData[Attribute::TYPE_TITLE]->setPattern("person/$sUser")
            ->setLanguage($oTranslate->getTargetLanguage())
            ->setType(Attribute::TYPE_TITLE)
            ->setAccess(\Defines\User\Access::getModApprove())
            ->setContent($sUser)
            ->setSearch($sUser)
            ->setUpdatedAt(new \DateTime);
        $em->persist($aData[Attribute::TYPE_TITLE]);

        // keywords
        $aData[Attribute::TYPE_KEYS]->setType(Attribute::TYPE_KEYS)
            ->setContent(sprintf($oTranslate->sys('LB_AUTO_PERSONAL_KEYS'), $sUser));
        $em->persist($aData[Attribute::TYPE_KEYS]);

        // description
        $aData[Attribute::TYPE_DESC]->setType(Attribute::TYPE_DESC)
            ->setContent(sprintf($oTranslate->sys('LB_AUTO_PERSONAL_DESC'), $sUser));
        $em->persist($aData[Attribute::TYPE_DESC]);

        // image
        $aData[Attribute::TYPE_IMG]->setType(Attribute::TYPE_IMG)
            ->setContent('/img/logo.jpg')
            ->setSearch(null);
        $em->persist($aData[Attribute::TYPE_IMG]);
        $em->flush();

        return $aData->getArrayCopy();
    }

    /**
     * Autocreate user information
     *
     * @param string $sUser
     */
    public function checkPersonListDesc($sUser, $sUrl, $sTitle = 'LB_PERSON_WORK')
    {
        $aData = (new \Data\ContentHelper)->findByUrl($sUrl, \Defines\Database\CrMain::CONTENT);
        if ($aData) {
            return;
        }

        $em = \System\Registry::connection();
        $oTranslate = \System\Registry::translation();

        $undef = (new \Data\UserHelper)->getUndefinedUser();

        $oContent = new \Data\Doctrine\Main\Content();
        $oContent->setPattern($sUrl)
            ->setLanguage($oTranslate->getTargetLanguage())
            ->setType(Attribute::TYPE_TITLE)
            ->setAuthor($undef)
            ->setAuditor($undef)
            ->setAccess(\Defines\User\Access::getModApprove())
            ->setContent($oTranslate->sys("{$sTitle}"))
            ->setSearch($oTranslate->sys("{$sTitle}"))
            ->setUpdatedAt(new \DateTime);
        try {
            $em->persist(clone $oContent);
            $em->flush();
        } catch (\Exception $e) {

        }

        // keywords
        $oContent->setType(Attribute::TYPE_KEYS)
            ->setContent(sprintf($oTranslate->sys('LB_AUTO_PERSONAL_KEYS'), $sUser));
        try {
            $em->persist(clone $oContent);
            $em->flush();
        } catch (\Exception $e) {

        }

        // description
        $oContent->setType(Attribute::TYPE_DESC)
            ->setContent($oTranslate->sys("{$sTitle}") . ' ' . $sUser);
        try {
            $em->persist(clone $oContent);
            $em->flush();
        } catch (\Exception $e) {

        }

        // image
        $oContent->setType(Attribute::TYPE_IMG)
            ->setContent('/img/logo.jpg')
            ->setSearch(null);
        try {
            $em->persist($oContent);
            $em->flush();
        } catch (\Exception $e) {

        }
    }
}
