<?php namespace Data;

use System\ArrayUndef;
use Defines\User\Access;
use Defines\Database\CrMain;

/**
 * Helper for a response content
 * @note `content*` tables
 *
 * @since 2015-07-20
 * @author Viachaslau Lyskouski
 */
class ContentHelper extends HelperAbstract
{

    protected function getTarget()
    {
        return CrMain::CONTENT;
    }

    /**
     * Convert url to a pattern (remove beginning '/' and extension type)
     *
     * @param string $sPattern
     * @return string
     */
    protected function updatePattern($sPattern)
    {
        // To avoid multiple instance on one collection with a variation of a temporary data
        $sUrl = str_replace('#!/', '/', $sPattern);
        if (strpos($sUrl, '?')) {
            $sUrl = substr($sUrl, 0, strpos($sUrl, '?'));
        }
        // Remove extansion
        $aTmpUrl = explode('.', $sUrl);
        if (in_array(end($aTmpUrl), \Defines\Extension::getList())) {
            array_pop($aTmpUrl);
        }
        // Remove slashes at the edges
        return trim(implode('.', $aTmpUrl), '/');
    }

    /**
     * Find all data from URL pattern
     * @param string $sPattern
     * @param string $Target - database table (default: `content`)
     * @return array<\Data\Doctrine\Main\Content>
     */
    public function findByUrl($sPattern, $Target = CrMain::CONTENT)
    {
        return $this->oEntityManager->getRepository($Target)->findBy(array(
            'pattern' => $sPattern,
            'language' => \System\Registry::translation()->getTargetLanguage()
        ));
    }

    /**
     * Get contant from the Database
     *
     * @param array $aValues [pattern=>[type=>[language=>[...]], ...]
     * @param boolean $bConvert
     * @return ArrayUndef
     */
    public function find($aValues, $bConvert = true, $bSkipLang = false)
    {
        $aTypes = array();
        $aLang = array();
        $aPatternConvert = array();

        $qb = new \Doctrine\ORM\QueryBuilder($this->getEntityManager());
        $qb->select('c')
            ->from(CrMain::CONTENT, 'c');

        // Prepare request parameters
        $i = 0;
        foreach ($aValues as $sPattern => $aParams) {
            foreach ($aParams as $sType => $aValues) {
                foreach (array_keys($aValues) as $sLanguage) {
                    $qb->orWhere("c.pattern = :pattern{$i} AND c.type = :type{$i} AND c.language = :language{$i}");

                    $qb->setParameter("pattern{$i}", $sPattern);
                    $qb->setParameter("type{$i}", $sType);
                    $qb->setParameter("language{$i}", $sLanguage);

                    $i++;
                }
            }
        }

        // Find data
        $aDbValues = $qb->getQuery()->execute();

        if ($bConvert) {
            $aDbValues = $this->covContent($aDbValues);
        }
        return $aDbValues;
    }

    /**
     * Save blob data
     *
     * @param string $sPattern
     * @param string $sType
     * @param string $sData
     * @return string - Resource #...
     */
    public function saveBlob($sPattern, $sType, $sData)
    {
        $oUser = \System\Registry::user()->getEntity();
        $sLang = \System\Registry::translation()->getTargetLanguage();
        $em = \System\Registry::connection();
        // @todo save blob file
        $oBlob = $em->getRepository(CrMain::CONTENT_BLOB)->findOneBy([
            'pattern' => $sPattern,
            'type' => $sType,
            'language' => $sLang
        ]);
        if (!$oBlob) {
            $oBlob = new \Data\Doctrine\Main\ContentBlob();
            $oBlob->setAuthor($oUser)
                ->setType($sType)
                ->setPattern($sPattern)
                ->setLanguage($sLang);
        } elseif ($oUser !== $oBlob->getAuthor()) {
            throw new \Error\Validation(\Data\UserHelper::getUsername($oBlob->getAuthor()));
        }
        $oBlob->setContent($sData)
            ->setUpdatedAt(new \DateTime);
        $em->persist($oBlob);
        $em->flush($oBlob);
        return $oBlob->getId();
    }

    public function delTmpBlob($oUser, $sLang, $sPattern, $sType)
    {
        $a = $this->getEntityManager()->getRepository(CrMain::CONTENT_BLOB)->findBy(array(
            'author' => $oUser,
            'language' => $sLang,
            'pattern' => $sPattern
        ));
        foreach ($a as $o) {
            $this->getEntityManager()->remove($o);
        }
    }

    public function getBlob($id)
    {
        return $this->getEntityManager()->getRepository(CrMain::CONTENT_BLOB)->find($id);
    }

    protected function covContent($aDbValues)
    {
        $aContent = new ArrayUndef();
        /* @var $oContent \Data\Doctrine\Main\Content */
        foreach ($aDbValues as $oContent) {
            $aContent[$oContent->getPattern()][$oContent->getType()][$oContent->getLanguage()] = $oContent->getContent();
        }
        return $aContent;
    }

    /**
     * Add temporary information for a missing statements
     *
     * @param array $aValues [pattern=>[type=>[language=>[...]], ...]
     */
    public function addTemporary($aValues)
    {
        $oContent = new Doctrine\Main\Content();
        $oContent->setAccess(Access::EDIT . Access::TRANSLATE . Access::READ);
        $oContent->setUpdatedAt(new \DateTime());

        $aKeys = \System\Registry::translation()->getBasicPageKeys();
        $bUpdate = false;
        foreach ($aValues as $sPattern => $aTypes) {
            if (array_diff(array_keys($aTypes), $aKeys)) {
                continue;
            }
            $oContent->setPattern($this->updatePattern($sPattern));
            foreach ($aTypes as $sType => $aLang) {
                $oContent->setType($sType);
                foreach (array_keys($aLang) as $sLanguage) {
                    if ('og:image' === $sType) {
                        $oContent->setContent('/img/logo.jpg');
                    } else {
                        $oContent->setContent("{ $sLanguage : $sPattern }");
                    }
                    $oContent->setLanguage($sLanguage);
                    $this->oEntityManager->persist(clone $oContent);
                    $bUpdate = true;
                }
            }
        }
        if ($bUpdate) {
            $this->oEntityManager->flush();
        }
    }
}
