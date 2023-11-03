<?php namespace Modules\Person\Work\Article;

/**
 * Model to create new article
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Model extends \Modules\AbstractModel
{

    /**
     * Get initial url for topic
     * @abstract for other classes
     *
     * @return string
     */
    public function getUrl()
    {
        return 'person/work/article';
    }

    /**
     * Get list of categories
     * @abstract for other classes
     *
     * @return array
     */
    public function getTyped()
    {
        return \Defines\Catalog::getMind();
    }

    /**
     * Get fields for insert into the Database
     *
     * @param array $aPost - is used to combine required fields
     * @return array
     */
    protected function getFields($aPost)
    {
        return array('og:title', 'description', 'og:image', 'keywords', 'action');
    }

    /**
     * Get list of categories
     * @abstract for other classes
     *
     * @return array
     */
    public function getCategories()
    {
        return (new \System\Converter\Massive)->getCategories($this->getTyped());
    }

    protected function getEntities($key)
    {
        return \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW)->findBy([
            'pattern' => "{$this->getUrl()}/{$key}",
            'language' => \System\Registry::translation()->getTargetLanguage()
        ], [
            'updatedAt' => 'ASC'
        ]);
    }

    public function getDescriptions($id)
    {
        $rep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        $title = $rep->find($id);

        if (!$title || $title->getAuthor() !== \System\Registry::user()->getEntity()) {
            throw new \Error\Validation('Draft is missing');
        }

        $result = $rep->findBy(array(
            'pattern' => $title->getPattern(),
            'type' => \Defines\Content\Attribute::getBasicList()
        ));
        $list = array();
        foreach ($result as $o) {
            $list[$o->getType()] = $o;
        }
        return $list;
    }

    /**
     * Send for review by Auditor
     *
     * @param integer $key
     */
    public function send4Review($key)
    {
        $em = \System\Registry::connection();
        $em->beginTransaction();
        /* @var $o \Data\Doctrine\Main\ContentNew */
        foreach ($this->getEntities($key) as $o) {
            if ($o->getType() === \Defines\Content\Attribute::TYPE_REPLY) {
                throw new \Error\Validation('Change content in accordance with auditor\'s comment!');
            }
            $o->setAccess(\Defines\User\Access::getAudit());
            $o->setAuditor(null);
            $em->persist($o);
        }
        $em->flush();
        $em->commit();
    }

    /**
     * Clear content values at the beginning
     *
     * @param integer $key
     */
    public function clearContent($key)
    {
        $em = \System\Registry::connection();
        $result = $this->getEntities($key);
        /* @var $o \Data\Doctrine\Main\ContentNew */

        $list = array_diff(\Defines\Content\Attribute::getList(), array(\Defines\Content\Attribute::TYPE_REPLY));
        foreach ($result as $o) {
            if (!in_array($o->getType(), $list, true)) {
                $em->remove($o);
            }
        }
        $em->flush();
    }

    /**
     * Add content value
     *
     * @param integer $key
     * @param integer $num
     * @param string $content
     */
    public function addContent($key, $num, $content)
    {
        $em = \System\Registry::connection();
        $o = new \Data\Doctrine\Main\ContentNew();
        $filter = new \System\Converter\Content($content);
        $value = $filter->getHtml();

        if ($filter->getText()) {
            $o->setAccess(\Defines\User\Access::getNew())
                ->setAuthor(\System\Registry::user()->getEntity())
                ->setContent($value)
                ->setLanguage(\System\Registry::translation()->getTargetLanguage())
                ->setPattern("{$this->getUrl()}/{$key}")
                ->setType("content#{$num}")
                ->setUpdatedAt(new \DateTime);
            $em->persist($o);
            $em->flush();
        }
    }

    /**
     * Update current content
     *
     * @param array $list
     * @return string - path
     * @throws \Error\Validation
     */
    public function updateContent(array $list)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT_NEW);

        foreach ($list as $id => $content) {
            /* @var $o \Data\Doctrine\Main\ContentNew */
            $o = $rep->find($id);
            if (!$o || $o->getAuthor() !== \System\Registry::user()->getEntity()) {
                throw new \Error\Validation('Draft is not yours');
            }
            $filter = new \System\Converter\Content($content);
            $o->setContent($filter->getText());
            $em->persist($o);
        }
        $em->flush();
        return $o->getPattern();
    }

    /**
     * Get actual data
     *
     * @param integer $key
     * @return array<\Data\Doctrine\Main\ContentNew>
     */
    public function getActual($key)
    {
        $result = $this->getEntities($key);
        $list = array(
            'firstPage' => true
        );
        /* @var $o \Data\Doctrine\Main\ContentNew */
        foreach ($result as $o) {
            if (strpos($o->getType(), 'content#') === 0) {
                $list['firstPage'] = false;
            }
            $list[$o->getType()] = $o->getContent();
        }
        $list['pattern'] = $o->getPattern();
        $list['url'] = $this->getUrl();
        $list['id'] = str_replace("{$this->getUrl()}/", '', $o->getPattern());
        $list['list'] = $result;
        return $list;
    }

    protected function createInTransaction($aPost, $sUrl, $oManager, $oHelper)
    {
        $oUser = \System\Registry::user()->getEntity();
        $sLang = \System\Registry::translation()->getTargetLanguage();
        // Get Id
        $o = new \Data\Doctrine\Main\ContentNew();
        $o->setPattern($sUrl)->setUpdatedAt(new \DateTime());
        $oManager->persist($o);
        $oManager->flush();
        $sUrl .= '/' . $o->getId();
        // Add new data
        foreach ($this->getFields($aPost) as $sType) {
            if (!isset($aPost[$sType])) {
                throw new \Error\Validation("Missing madatory `$sType` field!");
            }
            $sData = $aPost[$sType];
            if ($sType === 'og:image' && strpos($sData, 'image/') !== false) {
                $id = $oHelper->saveBlob($sUrl, $sType, $sData);
                $sData = "/files/$id";
            }

            $oContent = new \System\Converter\Content($sData);

            if (is_null($o)) {
                $o = new \Data\Doctrine\Main\ContentNew();
            }
            $o->setAuthor($oUser)
                ->setType($sType)
                ->setContent($oContent->getText())
                ->setPattern($sUrl)
                ->setLanguage($sLang)
                ->setUpdatedAt(new \DateTime())
                ->setAccess(\Defines\User\Access::getNew());
            $oManager->persist($o);
            $o = null;
        }
        return $sUrl;
    }

    /**
     * Create new draft
     *
     * @param array $aPost
     * @param string $sInitialUrl
     * @param array $aCategories
     * @throws \Error\Validation
     * @return string - url for redirect
     */
    public function create($aPost, $sInitialUrl, $aCategories)
    {
        $oHelper = new \Data\ContentHelper();
        $oManager = $oHelper->getEntityManager();
        $oManager->getConnection()->beginTransaction();
        try {
            if (array_key_exists('category', $aPost)) {
                $aPost['keywords'] = implode(', ', $this->findKeywords($aPost['category'], $aCategories));
            }
            $sUrl = $this->createInTransaction($aPost, $sInitialUrl, $oManager, $oHelper);
            $oManager->flush();
            $oManager->getConnection()->commit();
        } catch (\Exception $e) {
            $oManager->getConnection()->rollBack();
            throw new \Error\Validation('Draft cannot be saved');
        }
        return $sUrl;
    }

    /**
     * Prepare a list by a title
     *
     * @param string $sTitle
     * @param array $aCategories
     */
    protected function findKeywords($sTitle, $aCategories, $aResult = array())
    {
        $target = isset($aCategories['title']);
        $tmp = null;
        if ($target) {
            $aResult[] = $aCategories['title'];
        }
        // Gotcha! Finalize search
        if (end($aResult) === $sTitle) {
            $tmp = $aResult;
            // Go throuhg all sub-categories
        } else {
            // Subcategory
            if (isset($aCategories['sub'])) {
                $tmp = $this->findKeywords($sTitle, $aCategories['sub'], $aResult);
                // Elements of subcategory
            } elseif (is_array($aCategories) && !$target) {
                foreach ($aCategories as $aList) {
                    $tmp = $this->findKeywords($sTitle, $aList, $aResult);
                    // Gotcha! Finalize search
                    if (!is_null($tmp)) {
                        break;
                    }
                }
            }
        }
        return $tmp;
    }
}
