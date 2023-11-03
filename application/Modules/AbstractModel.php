<?php namespace Modules;

use Defines\Content\Attribute;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules
 */
abstract class AbstractModel
{
    /**
     * Prepare unified list [type => entity]
     *
     * @param \Data\Doctrine\Main\Content $entity
     * @param string $repType
     * @return array
     */
    public function prepareContentList($entity, $repType = \Defines\Database\CrMain::CONTENT)
    {
        $rep = \System\Registry::connection()->getRepository($repType);
        $result = $rep->findBy(array(
            'pattern' => $entity->getPattern(),
            'type' => Attribute::getBasicList(),
            'language' => $entity->getLanguage()
        ));
        $list = array();
        foreach ($result as $o) {
            $list[$o->getType()] = $o;
        }
        return $list;
    }



    /**
     * Check info
     *
     * @sample $sType = 'search'
     *     $oTranslate->sys('LB_AUTO_SEARCH_TITLE')
     *     $oTranslate->sys('LB_AUTO_SEARCH_KEYS')
     *     $oTranslate->sys('LB_AUTO_SEARCH_DESC')
     * @sample $sType = 'author'
     *     $oTranslate->sys('LB_AUTO_AUTHOR_TITLE')
     *     $oTranslate->sys('LB_AUTO_AUTHOR_KEYS')
     *     $oTranslate->sys('LB_AUTO_AUTHOR_DESC')
     *
     * @param string $sTargetUrl
     * @param string $sWord
     * @param string $sType
     */
    public function autocreateInfo($sTargetUrl, $sWord, $sType = 'search')
    {
        $oTranslate = \System\Registry::translation();
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT);

        $sUrl = "$sTargetUrl/$sType/$sWord";
        $a = $rep->findOneBy(array(
            'pattern' => $sUrl,
            'language' => $oTranslate->getTargetLanguage(),
            'type' => Attribute::TYPE_TITLE
        ));

        $isCreate = false;
        $toReplace = array();
        // Create new description
        if (!$a) {
            $isCreate = true;
        // Remove broken description
        } elseif (substr($a->getContent(), 0, 1) === '{') {
            $isCreate = true;
            $list = $rep->findBy(array(
                'pattern' => $sUrl,
                'language' => $oTranslate->getTargetLanguage()
            ));
            foreach ($list as $o) {
                $toReplace[$o->getType()] = $o;
            }
        }

        if ($isCreate) {
            $sUpType = strtoupper($sType);
            $oContent = new \Data\Doctrine\Main\Content();
            $oContent->setPattern($sUrl)
                ->setLanguage($oTranslate->getTargetLanguage())
                ->setAccess(\Defines\User\Access::getModApprove())
                ->setContent2(null)
                ->setUpdatedAt(new \DateTime);

            $toReplace = new \System\ArrayUndef($toReplace);
            $toReplace->setUndefined(function() use ($oContent) {
                return clone $oContent;
            });

            // title
            $toReplace[Attribute::TYPE_TITLE]->setType(Attribute::TYPE_TITLE)
                ->setContent(sprintf($oTranslate->sys("LB_AUTO_{$sUpType}_TITLE"), $sWord))
                ->setSearch($sWord);
            $em->persist($toReplace[Attribute::TYPE_TITLE]);
            // keywords
            $toReplace[Attribute::TYPE_KEYS]->setType(Attribute::TYPE_KEYS)
                ->setContent(sprintf($oTranslate->sys("LB_AUTO_{$sUpType}_KEYS"), $sWord));
            $em->persist($toReplace[Attribute::TYPE_KEYS]);
            // description
            $toReplace[Attribute::TYPE_DESC]->setType(Attribute::TYPE_DESC)
                ->setContent(sprintf($oTranslate->sys("LB_AUTO_{$sUpType}_DESC"), $sWord));
            $em->persist($toReplace[Attribute::TYPE_DESC]);
            // image
            $toReplace[Attribute::TYPE_IMG]->setType(Attribute::TYPE_IMG)
                ->setContent('/img/logo.jpg')
                ->setSearch(null);
            $em->persist($toReplace[Attribute::TYPE_IMG]);

            $em->flush();
        }
    }

}