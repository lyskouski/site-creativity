<?php namespace Modules\Dev\Tasks\Translation\Text;

use Defines\Content\Attribute;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{

    public function getTask($id)
    {
        $oRep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        /* @var $oContent \Data\Doctrine\Main\ContentNew */
        $oContent = $oRep->find($id);
        $oUser = \System\Registry::user()->getEntity();
        $oTranslate = \System\Registry::translation();

        if (!$oContent) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_IS_MISSING'));
        } elseif ($oContent->getAuthor() !== $oUser) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_NOT_YOURS'));
        } elseif($oContent->getAuditor() !== $oUser) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_WAIT_APPROVEMENT'));
        }

        $aDb = $oRep->findBy(array(
            'pattern' => $oContent->getPattern(),
            'language' => $oContent->getLanguage(),
            'author' => $oUser,
            'type' => Attribute::getMandatory()
        ));
        // Form current list
        $aResult = array(
            'next' => (boolean) strpos((new \Engine\Request\Input)->getRefererUrl(), '/text/')
        );
        foreach ($aDb as $o) {
            $aResult[$o->getType()] = $o;
        }
        // Prepare a list for other languages
        $aResult['samples'] = new \System\ArrayUndef();

        $mPattern = $oContent->getPattern();
        $aConvert = (new \System\Converter\Massive)->getConvertable($mPattern . '.' . \Defines\Extension::HTML);
        if ($aConvert) {
            $aTmp = array();
            foreach (\Defines\Language::getList() as $lang) {
                $s = current($aConvert);
                $aTmp[] = str_replace(key($aConvert), $oTranslate->sys("{$s}", $lang), $mPattern);
            }
            $mPattern = $aTmp;
        }

        $aDbSamples = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT)
            ->findBy(array(
                'pattern' => $mPattern,
                'language' => array_diff(\Defines\Language::getList(), array($oContent->getLanguage())),
                'type' => Attribute::getMandatory()
            ), array(
                'pattern' => 'ASC',
                'language' => 'ASC'
            ));
        foreach ($aDbSamples as $o) {
            $aResult['samples'][$o->getLanguage()][$o->getType()] = $o;
        }

        return $aResult;
    }

    protected function prepareQuery($query, $lang)
    {
        $query->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where(
                'c.language = :language',
                'c.author is null',
                'c.type = :type',
                'c.pattern NOT LIKE :pattern'
            )
            ->setParameters(array(
                'language' => $lang,
                'type' => Attribute::TYPE_TITLE,
                'pattern' => 'book/overview/i%'
            ));
        return $query;
    }

    public function getTaskList()
    {
        $lang = \System\Registry::translation()->getTargetLanguage();
        $query = \System\Registry::connection()->createQueryBuilder()->select('c');
        return $this->prepareQuery($query, $lang)
                ->setMaxResults(20)
                ->setFirstResult(0)
                ->getQuery()->getResult();
    }

    /**
     * Get not ready tasks
     *
     * @param string $lang - const from \Defines\Language
     * @return integer
     */
    public function getSumTasks($lang)
    {
        $em = \System\Registry::connection();
        $oQuery = $em->createQueryBuilder();
        $this->prepareQuery($oQuery->select('COUNT(c.id)'), $lang);

        return $oQuery->getQuery()->getSingleScalarResult();
    }

    public function checkTask()
    {
        $em = \System\Registry::connection();
        $result = $em->createQueryBuilder()->select('c.id')
            ->from(\Defines\Database\CrMain::CONTENT_NEW, 'c')
            ->where(
                'c.language = :language',
                'c.author = :author',
                'c.auditor = :auditor',
                'c.access = :access',
                'c.pattern NOT LIKE :pattern'
            )->setParameters(array(
                'language' => \System\Registry::translation()->getTargetLanguage(),
                'author' => \System\Registry::user()->getEntity(),
                'auditor' => \System\Registry::user()->getEntity(),
                'access' => \Defines\User\Access::getAccessNew(),
                'pattern' => 'book/overview/i%'
            ))
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()
            ->getScalarResult();

        return $result ? current($result[0]) : 0;
    }

    /**
     * Put random missing to a content_new table for the current user
     *
     * @return integer
     */
    public function getNewTask($id = null)
    {
        $em = \System\Registry::connection();
        /* @var $task \Data\Doctrine\Main\Content */
        if ($id) {
            $task = $em->find(\Defines\Database\CrMain::CONTENT, $id);
        } else {
            $lang = \System\Registry::translation()->getTargetLanguage();
            $query = $em->createQueryBuilder()->select('c');
            $task = $this->prepareQuery($query, $lang)
                ->setMaxResults(1)
                ->setFirstResult(0)
                ->getQuery()->getSingleResult();
        }

        if ($task->getAuthor()) {
            throw new \Error\Validation('Already taken by another translator');
        }

        $oUser = \System\Registry::user()->getEntity();
        // Find required fields
        foreach ($this->findTask($task) as $task) {
            $task->setAuthor($oUser);
            $em->persist($task);
            $oNewContent = $this->persistNewTask($task);
            $em->persist($oNewContent);
        }
        $em->flush();
        return $oNewContent->getId();
    }

    /**
     * Find content entity
     *
     * @param \Data\Doctrine\Main\Content|\Data\Doctrine\Main\ContentNew $oContent
     * @param string $sRepository - const from \Defines\Database\CrMain
     * @return array<\Data\Doctrine\Main\Content|\Data\Doctrine\Main\ContentNew>
     */
    public function findTask($oContent, $sRepository = \Defines\Database\CrMain::CONTENT)
    {
        return \System\Registry::connection()->getRepository($sRepository)->findBy(array(
            'pattern' => $oContent->getPattern(),
            'language' => $oContent->getLanguage(),
            'type' => Attribute::getMandatory()
        ));
    }

    /**
     *
     * @param \Data\Doctrine\Main\Content $oContent
     * @return \Data\Doctrine\Main\ContentNew
     */
    public function persistNewTask(\Data\Doctrine\Main\Content $oContent)
    {
        $oUser = \System\Registry::user()->getEntity();
        $oNewContent = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW)
            ->findOneBy(array(
                'pattern' => $oContent->getPattern(),
                'type' => $oContent->getType(),
                'language' => $oContent->getLanguage()
            ));
        if (!$oNewContent) {
            $oNewContent = new \Data\Doctrine\Main\ContentNew();
        }
        $oNewContent->setAccess(\Defines\User\Access::getAccessNew())
            ->setContent($oContent->getContent())
            ->setContent2($oContent)
            ->setAuthor($oUser)
            ->setAuditor($oUser)
            ->setLanguage($oContent->getLanguage())
            ->setPattern($oContent->getPattern())
            ->setType($oContent->getType())
            ->setUpdatedAt(new \DateTime());
        return $oNewContent;
    }

    public function saveTask($aList)
    {
        $oConv = new \System\Converter\StringUtf();
        $em = \System\Registry::connection();
        $oRep = $em->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        $oTran = \System\Registry::translation();
        $iTitleSize = 70;// - $oConv->strlen($oTran->sys('LB_SITE_TITLE'));

        foreach ($aList as $id => $content) {
            /* @var $oNewContent \Data\Doctrine\Main\ContentNew */
            $oNewContent = $oRep->find($id);
            if (!$oNewContent) {
                throw new \Error\Validation($oTran->sys('LB_TASK_IS_MISSING') . ': '. $id);
            }
            // Mandatory fields
            if (!in_array($oNewContent->getType(), [Attribute::TYPE_REPLY, 'content#0', ''])) {
                $s = trim(str_replace(array('"', "\n", "\r"), array('`'), strip_tags($content)));
                if ($s !== trim($content) || !$s) {
                    throw new \Error\Validation($oTran->sys('LB_ERROR_ONLY_TEXT') . ': ' . $oNewContent->getType());
                }
                if ($s[0] === '{') {
                    throw new \Error\Validation($oTran->sys('LB_ERROR_NOT_READY') . ': '. $oNewContent->getType());
                }
                if ($oNewContent->getType() === Attribute::TYPE_TITLE && $oConv->strlen($s) > $iTitleSize) {
                    throw new \Error\Validation(sprintf($oTran->sys('LB_ERROR_LEGTH_LIMITATION') . ': '. $oTran->sys('LB_PERSON_TITLE') . '('. $oConv->strlen($s) .')', $iTitleSize) );
                } elseif ($oConv->strlen($s) > 150) {
                    throw new \Error\Validation(sprintf($oTran->sys('LB_ERROR_LEGTH_LIMITATION') . ': '. $oNewContent->getType() . '('. $oConv->strlen($s) .')', 150) );
                }
                $oNewContent->setContent($s);
            // Add comment
            } elseif ($oNewContent->getType() === 'content#0') {
                $filter = new \System\Converter\Content($content);
                $oNewContent->setContent($filter->getHtml());
            // All others has to be deleted
            } else {
                $em->remove($oNewContent);
                continue;
            }

            $oNewContent->setAuditor(null)
                ->setAccess(\Defines\User\Access::getAudit());
            $em->persist($oNewContent);
        }
        $em->flush();

    }
}
