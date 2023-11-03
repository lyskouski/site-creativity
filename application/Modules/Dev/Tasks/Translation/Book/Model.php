<?php namespace Modules\Dev\Tasks\Translation\Book;

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

    public function getTaskList()
    {
        $lang = \System\Registry::translation()->getTargetLanguage();
        $query = \System\Registry::connection()->createQueryBuilder()->select('c');
        return $query->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where(
                'c.language = :language',
                'c.author is null',
                'c.type = :type',
                'c.pattern LIKE :pattern'
            )
            ->setParameters(array(
                'language' => $lang,
                'type' => Attribute::TYPE_TITLE,
                'pattern' => 'book/overview/i%'
            ))
            ->setMaxResults(20)
            ->setFirstResult(0)
            ->getQuery()->getResult();
    }

    public function getTask($id)
    {
        $oRep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        $oTranslate = \System\Registry::translation();
        /* @var $oContent \Data\Doctrine\Main\ContentNew */
        $oContent = $oRep->find($id);
        $oUser = \System\Registry::user()->getEntity();

        if (!$oContent) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_IS_MISSING'));
        } elseif ($oContent->getAuthor() !== $oUser) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_NOT_YOURS'));
        } elseif ($oContent->getAuditor() !== $oUser) {
            throw new \Error\Validation($oTranslate->sys('LB_TASK_WAIT_APPROVEMENT'));
        }

        $aDb = $oRep->findBy(array(
            'pattern' => $oContent->getPattern(),
            'language' => $oContent->getLanguage(),
            'author' => $oUser
        ));
        // Form current list
        $aResult = array(
            'next' => (boolean) strpos((new \Engine\Request\Input)->getRefererUrl(), '/book/'),
            'list' => array()
        );
        foreach ($aDb as $o) {
            $aResult['list'][$o->getType()] = $o;
            if (in_array($o->getType(), ['og:title', 'og:reply'])) {
                $aResult[$o->getType()] = $o;
            }
        }

        return $aResult;
    }

    /**
     * Get not ready tasks
     *
     * @param string $sLang - const from \Defines\Language
     * @return integer
     */
    public function getSumTasks($sLang)
    {
        $oQuery = \System\Registry::connection()->createQueryBuilder();
        $oQuery->select('count(c.id)')
            ->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where('c.language = :language', 'c.author is null', 'c.type = :type')
            ->setParameters(array(
                'language' => $sLang,
                'type' => 'pageCount'
            )
        );
        return $oQuery->getQuery()->getSingleScalarResult();
    }

    public function checkTask()
    {
        $oQuery = \System\Registry::connection()->createQueryBuilder();
        $result = $oQuery->select('c.id')
            ->from(\Defines\Database\CrMain::CONTENT_NEW, 'c')
            ->where(
                'c.language = :language',
                'c.author = :author',
                'c.auditor = :auditor',
                'c.access = :access',
                'c.type = :type'
            )->setParameters(array(
                'language' => \System\Registry::translation()->getTargetLanguage(),
                'author' => \System\Registry::user()->getEntity(),
                'auditor' => \System\Registry::user()->getEntity(),
                'access' => \Defines\User\Access::getAccessNew(),
                'type' => 'pageCount'
            ))->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()->getScalarResult();

        return $result ? current($result[0]) : 0;
    }

    /**
     * Put random missing to a content_new table for the current user
     *
     * @return integer
     */
    public function getNewTask()
    {
        $oManager = \System\Registry::connection();
        $oQuery = $oManager->createQueryBuilder();
        $result = $oQuery->select('c')
            ->from(\Defines\Database\CrMain::CONTENT, 'c')
            ->where(
                'c.language = :language',
                'c.author is null',
                'c.type = :type'
            )->setParameters(array(
                'language' => \System\Registry::translation()->getTargetLanguage(),
                'type' => 'pageCount'
            ))->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()->getResult();
        $oContent = current($result);

        $oUser = \System\Registry::user()->getEntity();
        // Find required fields
        foreach ($this->findTask($oContent) as $oContent) {
            $oContent->setAuthor($oUser);
            $oManager->persist($oContent);
            $oNewContent = $this->persistNewTask($oContent);
            $oManager->persist($oNewContent);
        }
        $oManager->flush();
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
            'language' => $oContent->getLanguage()
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
        $oNewContent = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW)->findOneBy(array(
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
        $oManager = \System\Registry::connection();
        $oRep = $oManager->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        $oTran = \System\Registry::translation();
        $iTitleSize = 250; // - $oConv->strlen($oTran->sys('LB_SITE_TITLE'));
        foreach ($aList as $id => $content) {
            /* @var $oNewContent \Data\Doctrine\Main\ContentNew */
            $oNewContent = $oRep->find($id);
            if (!$oNewContent) {
                throw new \Error\Validation(\System\Registry::translation()->sys('LB_TASK_IS_MISSING') . ': ' . $id);
            }
            // Save image
            if ($oNewContent->getType() === 'og:image' && strpos($content, 'image/') !== false) {
                $id = (new \Data\ContentHelper)->saveBlob($oNewContent->getPattern(), $oNewContent->getType(), $content);
                $content = "/files/$id";
            }
            // Mandatory fields
            if (!in_array($oNewContent->getType(), [Attribute::TYPE_REPLY, 'content#0', ''])) {
                $s = trim(str_replace(array('"', "\n", "\r"), array('`'), strip_tags($content)));
                if ($s !== trim($content)) {
                    throw new \Error\Validation(\System\Registry::translation()->sys('LB_ERROR_ONLY_TEXT') . ': ' . $oNewContent->getType());
                }
                if ($s && $s[0] === '{') {
                    throw new \Error\Validation(\System\Registry::translation()->sys('LB_ERROR_NOT_READY') . ': ' . $oNewContent->getType());
                }
                if ($oNewContent->getType() === Attribute::TYPE_TITLE && $oConv->strlen($s) > $iTitleSize) {
                    throw new \Error\Validation(sprintf($oTran->sys('LB_ERROR_LEGTH_LIMITATION') . ': ' . $oTran->sys('LB_PERSON_TITLE') . '(' . mb_strlen($s) . ')', $iTitleSize));
                } elseif ($oConv->strlen($s) > 150) {
                    throw new \Error\Validation(sprintf(\System\Registry::translation()->sys('LB_ERROR_LEGTH_LIMITATION') . ': ' . $oNewContent->getType() . '(' . mb_strlen($s) . ')', 150));
                }
                $oNewContent->setContent($s);
            // Add comment
            } elseif ($oNewContent->getType() === 'content#0') {
                $filter = new \System\Converter\Content($content);
                $oNewContent->setContent($filter->getHtml());
            // All others has to be deleted
            } else {
                $oManager->remove($oNewContent);
                continue;
            }

            $oNewContent->setAuditor(null)
                    ->setAccess(\Defines\User\Access::getAudit());
            $oManager->persist($oNewContent);
        }
        $oManager->flush();
    }

}
