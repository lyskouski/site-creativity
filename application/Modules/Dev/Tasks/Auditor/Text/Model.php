<?php namespace Modules\Dev\Tasks\Auditor\Text;

use Defines\Database\CrMain;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Dev\Tasks\Model
{

    const AUDITOR_MESSAGE = 'og:reply';

    /**
     * @var \Data\ContentHelper
     */
    protected $oHelper;

    public function __construct()
    {
        $this->oHelper = new \Data\ContentHelper();
    }

    /**
     * Count tasks that are not ready
     *
     * @return array
     */
    public function getSumTasks()
    {
        $aResult = array();
        foreach (\Defines\Language::getList() as $sLang) {
            $aResult[$sLang] = $this->getAuditorTasks($sLang);
        }
        return $aResult;
    }

    /**
     * Get user task
     * @return array<\Data\Doctrine\Main\ContentNew>
     */
    public function getTask() {
        $oRep = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT_NEW);
        $aData = $oRep->findBy(array(
            'language' => \System\Registry::translation()->getTargetLanguage(),
            'auditor' => \System\Registry::user()->getEntity(),
        ), array(
            'id' => 'ASC'
        ));
        $aResult = array();
        foreach ($aData as $o) {
            if (substr($o->getAccess(), 1, 1) == \Defines\User\Access::AUDIT) {
                $aResult[] = $o;
            }
        }
        return $aResult;
    }

    /**
     * Approve user task
     */
    public function approveTask() {
        $oTranslate = \System\Registry::translation();
        $oManager = \System\Registry::connection();
        $oManager->getConnection()->beginTransaction();

        $repContent = $oManager->getRepository(CrMain::CONTENT);

        /* @var $oNewContent \Data\Doctrine\Main\ContentNew */
        $aList = $this->getTask();
        $pattern = '';

        $updatedList = new \System\ArrayUndef([]);
        $updatedList->setUndefined(null);

        $specialCase = '';

        $iPages = 0;
        $book = null;
        $fillPattern = true;
        $access = \Defines\User\Access::EDIT . \Defines\User\Access::TRANSLATE . \Defines\User\Access::READ;
        foreach ($aList as $oNewContent) {
            if (!$pattern) {
                $pattern = $oNewContent->getPattern();
            }
            /* @var $oNewContent \Data\Doctrine\Main\Content */
            $oContent = $oNewContent->getContent2();
            // Just in case...
            if (!$oContent) {
                $oContent = $repContent->findOneBy(array(
                    'type' => $oNewContent->getType(),
                    'language' => $oNewContent->getLanguage(),
                    'pattern' => $oNewContent->getPattern()
                ));
            }
            // Create new
            if (!$oContent) {
                $oContent = new \Data\Doctrine\Main\Content();
            } elseif ($fillPattern) {
                $pattern = $oContent->getPattern();
                $access = $oContent->getAccess();
                $fillPattern = false;
            }

            // Create negative ISBN for non-relevant book (that wasn't published)
            if ($oNewContent->getType() === 'isbn' && strlen(trim($oNewContent->getContent())) === 0) {
                /* @var $book \Data\Doctrine\Main\Book */
                $minBook = $oManager->getRepository(CrMain::BOOK)->findOneBy([], ['id' => 'ASC']);
                if ($minBook) {
                    $minId = (int) $minBook->getId();
                    if ($minId > 0) {
                        $oNewContent->setContent(-1);
                    } else {
                        $oNewContent->setContent($minId - 1);
                    }
                } else {
                    $oNewContent->setContent(-1);
                }
            }

            if ($oNewContent->getType() !== self::AUDITOR_MESSAGE) {
                $sContent = trim($oNewContent->getContent());
                $conv = new \System\Converter\Content($sContent);

                $oContent->setAuditor($oNewContent->getAuditor())
                    ->setAccess($access)
                    ->setAuthor($oNewContent->getAuthor())
                    ->setContent($conv->getHtml())
                    ->setSearch($conv->getText())
                    ->setLanguage($oNewContent->getLanguage())
                    ->setPattern($pattern)
                    ->setType($oNewContent->getType())
                    ->setUpdatedAt($oNewContent->getUpdatedAt());
                $oManager->persist($oContent);
                // Convert pattern if it's an artwork (article, etc)
                if (strpos($pattern, 'person/work') === 0 && strpos($pattern, $oNewContent->getId())) {
                    /** @todo move to an auditor panel (for possibilities to change target) */
                    $tmp = str_replace(['person/work/', '/' . $oNewContent->getId()], '', $pattern);
                    switch ($tmp) {
                        case 'article': $pattern = 'mind/article/i'; break;
                        case 'prose': $pattern = 'oeuvre/prose/i'; break;
                        case 'poetry': $pattern = 'oeuvre/poetry/i'; break;
                        case 'drawing': $pattern = 'oeuvre/drawing/i'; break;
                        case 'music': $pattern = 'oeuvre/music/i'; break;
                        case 'book':  $pattern = 'book/overview/i'; break;
                        case 'book/series':  $pattern = 'book/series/i'; break;
                        default: $pattern = 'trash/i';
                    }
                    $oManager->flush($oContent);
                    $pattern .= $oContent->getId();
                    $oContent->setPattern($pattern);
                    $oManager->persist($oContent);
                }
                // Save temporary files
                /** @todo save temporary files inside content */
                if ($oContent->getType() === 'og:image' || $oContent->getType() === 'image') {
                    // Grab external images
                    $url = filter_var($sContent, FILTER_SANITIZE_URL);
                    if ($url) {
                        $tmpImage = new \Data\File\Image($url);
                        if ($tmpImage->isBlob()) {
                            $sContent = '/files/' . (new \Data\ContentHelper)->saveBlob(
                                $oContent->getPattern(),
                                $oContent->getType(),
                                $tmpImage->getContent()
                            );
                        }
                    }
                    // Store file to server
                    if (strpos($sContent, '/files/') === 0) {
                        $oContent->setContent((new \Modules\Files\Model)->saveFile(
                            substr($sContent, strlen('/files/')),
                            \Data\UserHelper::getUsername($oNewContent->getAuthor()),
                            filter_var($pattern, FILTER_SANITIZE_NUMBER_INT)
                        ));
                        $oManager->persist($oContent);
                    }
                }
            }
            // Check if it's a book overview creation
            if ($oNewContent->getType() === 'isbn') {
                $specialCase = 'book';
                /* @var $book \Data\Doctrine\Main\Book */
                $book = $oManager->find(CrMain::BOOK, $oNewContent->getContent());
                if (!$book) {
                    $book = new \Data\Doctrine\Main\Book();
                } elseif ($pattern !== $book->getContent()->getPattern()) {
                    throw new \Error\Validation(
                        $oTranslate->sys('LB_HEADER_303') . ': /' . $book->getContent()->getPattern(),
                        \Defines\Response\Code::E_CONFLICT
                    );
                }
            }
            // Special case for series
            if (strpos($pattern, 'book/series') !== false) {
                $specialCase = 'book/series';
            }

            // Count pages
            if (strpos($oNewContent->getType(), 'content#') === 0) {
                $iPages++;
            }
            $updatedList[$oNewContent->getType()] = $oContent;
            $oManager->remove($oNewContent);
        }
        $oManager->flush();

        switch ($specialCase) {
            // Add new book
            case 'book':
                $iPages = (int) $updatedList['pageCount']->getContent();
                $book->setIsbn((int) ltrim($updatedList['isbn']->getContent(), '0'))
                    ->setContent($updatedList['og:title'])
                    ->setAuthor($updatedList['author']->getContent())
                    ->setTitle($updatedList['og:title']->getContent())
                    ->setYear((int) substr($updatedList['date']->getContent(), 0, 4))
                    ->setPages($iPages);
                $oManager->persist($book);
                break;
            // Add series
            case 'book/series':
                $list = explode(',', $updatedList['content#0']->getContent());
                $iPages = sizeof($list);
                $tmp = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::BOOK)->findById($list);
                /* @var $o \Data\Doctrine\Main\Book */
                foreach ($tmp as $o):
                    $tmp["{$o->getId()}"] = $o;
                endforeach;
                // Remove previous state $oManager->remove();
                $oManager->createQueryBuilder()
                    ->delete(CrMain::CONTENT_SERIES, 's')
                    ->where('s.series = :series')
                    ->setParameter('series', $updatedList['og:title'])
                    ->getQuery()->execute();
                // Add updated list
                foreach ($list as $isbn) {
                    if (!$isbn) {
                        continue;
                    }
                    $entity = new \Data\Doctrine\Main\ContentSeries();
                    $entity->setSeries($updatedList['og:title'])
                        ->setContent($tmp[$isbn]->getContent());
                    $oManager->persist($entity);
                }
                break;
        }
        // Update pages
        if ($iPages) {
            /* @var $oStat \Data\Doctrine\Main\ContentViews */
            $oStat = $oManager->getRepository(\Defines\Database\CrMain::CONTENT_VIEWS)->findOneByContent($updatedList['og:title']);
            if (!$oStat) {
                $oStat = new \Data\Doctrine\Main\ContentViews();
                $oStat->setContent($updatedList['og:title']);
            }
            $oStat->setContentCount($iPages);
            $oManager->persist($oStat);
        }
        $oManager->flush();
        $oManager->getConnection()->commit();
    }

    /**
     * Delete user's task
     */
    public function deleteTask()
    {
        $oManager = $this->oHelper->getEntityManager();
        /* @var $oNewContent \Data\Doctrine\Main\ContentNew */
        $aList = $this->getTask();
        foreach ($aList as $oNewContent) {
            // detach
            $oNewContent->setContent2(null);
            $oManager->persist($oNewContent);
            $oManager->flush();
            // remove
            $oManager->remove($oNewContent);
            $oManager->flush();
        }

    }

    /**
     * Reject task with additional comment
     *
     * @param string $sReason
     */
    public function rejectTask( $sReason )
    {
        $aList = $this->getTask();
        $sAccess = \Defines\User\Access::getAccessNew();
        $oManager = $this->oHelper->getEntityManager();
        /* @var $oContent \Data\Doctrine\Main\ContentNew */
        foreach ($aList as $oContent) {
            $oContent->setAuditor($oContent->getAuthor());
            $oContent->setAccess($sAccess);
            $oManager->persist($oContent);
        }
        if ($sReason) {
            $oNewContent = new \Data\Doctrine\Main\ContentNew();
            $oNewContent->setAuditor($oContent->getAuditor())
                ->setAccess($sAccess)
                ->setAuthor($oContent->getAuthor())
                ->setContent($sReason)
                ->setLanguage($oContent->getLanguage())
                ->setPattern($oContent->getPattern())
                ->setType('og:reply')
                ->setUpdatedAt($oContent->getUpdatedAt());
            $oManager->persist($oNewContent);
        }
        $oManager->flush();
    }

    /**
     * Put random missing to a content_new table for the current user
     *
     * @return integer
     */
    public function getNewTask()
    {
        $sLang = \System\Registry::translation()->getTargetLanguage();
        $id = $this->getAuditorTasks($sLang, 'min');

        if (!$id) {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_TASK_IS_MISSING'));
        }

        $oRep = $this->oHelper->getEntityManager()->getRepository(CrMain::CONTENT_NEW);
        $oContent = $oRep->find($id);
        $aData = $oRep->findBy(array(
            'language' => $sLang,
            'pattern' => $oContent->getPattern(),
        ));
        // Find required fields
        $oManager = $this->oHelper->getEntityManager();
        $oUser = \System\Registry::user()->getEntity();
        foreach ($aData as $oContent) {
            $oContent->setAuditor($oUser);
            $oManager->persist($oContent);
        }
        $oManager->flush();
        return $oContent->getId();
    }


    /**
     * Get task unique identificator
     *
     * @return integer
     */
    public function checkTask() {
        $a = $this->getTask();
        $iTask =  0;
        if ($a) {
            $iTask = $a[0]->getId();
        }
        return $iTask;
    }

    /**
     * Get not ready tasks
     *
     * @param string $sLang - const from \Defines\Language
     * @return integer
     */
    public function getAuditorTasks($sLang, $sType = 'count')
    {
        $oQuery = $this->oHelper->getEntityManager()->createQueryBuilder();
        $oQuery->select($sType . '(c.id)')
            ->from(CrMain::CONTENT_NEW, 'c')
            ->where(
                'c.language = :language',
                'c.auditor is null',
                'c.type = :type',
                "c.access LIKE :access"
            )
            ->setParameters(array(
                'language' => $sLang,
                'type' => 'og:title',
                'access' => '_' .  \Defines\User\Access::AUDIT . '_'
            ));

        return $oQuery->getQuery()->getSingleScalarResult();
    }

    public function changeAuthor($username)
    {
        $em = $this->oHelper->getEntityManager();
        $em->beginTransaction();
        // Identify user
        $user = $em->getRepository(CrMain::USER)->findOneByUsername($username);
        if (!$user) {
            $user = new \Data\Doctrine\Main\User();
            $user->setUsername($username);
            // Add user role
            $userAccess = new \Data\Doctrine\Main\UserAccess();
            $userAccess->setUser($user);
            $userAccess->setAccess(
                $em->getRepository(CrMain::ACCESS)->findOneByTitle('LB_ACCESS_AUTHOR')
            );
            $em->persist($user);
            $em->persist($userAccess);
        }

        /* @var $oNewContent \Data\Doctrine\Main\ContentNew */
        foreach ($this->getTask() as $oNewContent) {
            $oNewContent->setAuthor($user);
            $em->persist($oNewContent);
        }

        $em->flush();
        $em->commit();
    }
}
