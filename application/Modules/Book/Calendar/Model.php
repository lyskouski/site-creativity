<?php namespace Modules\Book\Calendar;

use Defines\Database\CrMain;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    const URL_PATTERN = 'book/calendar/i';

    /**
     * Get list entity
     *
     * @param array $aParams
     * @return \Data\Doctrine\Main\Content
     * @throws \Error\Validation
     */
    public function getEntity(array $aParams)
    {
        $entity = null;
        $curr = current($aParams);
        if (is_string($curr) && $curr[0] === 'i') {
            $idList = substr($curr, 1);
            $entity = \System\Registry::connection()->find(\Defines\Database\CrMain::CONTENT, $idList);

            if (!$entity) {
                throw new \Error\Validation(
                    \Defines\Response\Code::getHeader(\Defines\Response\Code::E_NOT_FOUND, null),
                    \Defines\Response\Code::E_NOT_FOUND
                );
            }

            $oValid = new \Access\Validate\Check();
            if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted($entity)) {
                throw new \Error\Validation(
                    \Defines\Response\Code::getHeader(\Defines\Response\Code::E_FORBIDDEN, null),
                    \Defines\Response\Code::E_FORBIDDEN
                );
            }
        }
        return $entity;
    }

    /**
     * @param \Data\Doctrine\Main\Content $entity
     * @param array $list
     * @return string
     * @throws \Error\Validation
     */
    public function updateContent($entity, array $list)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT);

        foreach ($list as $id => $content) {
            /* @var $o \Data\Doctrine\Main\Content */
            $o = $rep->find($id);
            if ($entity->getPattern() !== $o->getPattern()) {
                throw new \Error\Validation('Is not yours');
            }
            if ($o->getType() === 'og:image' || $o->getType() === 'image') {
                $img = new \Data\File\Image($content);
                if ($img->isBlob()) {
                    $fileId = (new \Data\ContentHelper)->saveBlob($o->getPattern(), $o->getType(), $img->getContent());
                    $content = (new \Modules\Files\Model)->saveFile(
                        $fileId,
                        \System\Registry::user()->getEntity()->getUsername(),
                        $o->getId()
                    );
                }
            }
            $filter = new \System\Converter\Content($content);
            $o->setContent($filter->getText());
            $em->persist($o);
        }
        $em->flush();
        return $o->getPattern();
    }

    /**
     * Get all user's list
     *
     * @param boolean $isLanguage
     * @return array<\Data\Doctrine\Main\Content>
     */
    public function getAllList($isLanguage = true)
    {
        if (!\System\Registry::user()->isLogged()) {
            return array();
        }

        $search = \System\Registry::connection()->getRepository(CrMain::CONTENT)
            ->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', 'og:title')
            ->andWhere('c.pattern LIKE :pattern')
            ->setParameter('pattern', self::URL_PATTERN . '%')
            ->andWhere('c.author = :author')
            ->setParameter('author', \System\Registry::user()->getEntity());

        if ($isLanguage) {
            $search->andWhere('c.language = :language')
                ->setParameter('language', \System\Registry::translation()->getTargetLanguage());
        }

        return $search->orderBy('c.content', 'ASC')
                ->getQuery()->getResult();
    }

    public function createList($title)
    {
        $oTranslate = \System\Registry::translation();
        $user = \System\Registry::user()->getEntity();
        $pattern = self::URL_PATTERN;

        $entity = new \Data\Doctrine\Main\Content();
        $entity->setAccess(\Defines\User\Access::getNew())
            ->setAuthor($user)
            ->setAuditor($user)
            ->setContent($title)
            ->setSearch($title)
            ->setLanguage($oTranslate->getTargetLanguage())
            ->setType('og:title')
            ->setUpdatedAt(new \DateTime)
            ->setPattern($pattern);

        $description = clone $entity;
        $description->setType('description')
            ->setContent($oTranslate->sys('LB_BOOK_LIST') . ': ' . $title);

        $keywords = clone $entity;
        $keywords->setType('keywords')
            ->setContent($user->getUsername() . ', ' . $oTranslate->sys('LB_BOOK_LIST'));

        $em = \System\Registry::connection();
        $em->persist($entity);
        $em->flush();

        $url = $pattern . $entity->getId();
        $entity->setPattern($url);
        $em->persist($entity);
        $description->setPattern($url);
        $em->persist($description);
        $keywords->setPattern($url);
        $em->persist($keywords);

        $em->flush();
        return $url;
    }

    public function getSourceList($lang)
    {
        $search = new \Engine\Book\Search($lang);
        $list = array();
        /* @var $engine \Engine\Book\Search\HelperInterface */
        foreach ($search->getEngine() as $engine) {
            $className = get_class($engine);
            $list[] = array(
                'name' => substr($className, strrpos($className, '\\') + 1)
            );
        }
        return $list;
    }

    public function findBooks($isbn, $author, $title, $lang = null, $type = '')
    {
        /* @var $rep \Data\Model\BookRepository */
        $rep = \System\Registry::connection()->getRepository(CrMain::BOOK);
        $search = new \Engine\Book\Search($lang);
        if ($isbn || $author || $title) {
            $attr = array(
                \Engine\Book\Search::TYPE_AUTHOR => $author,
                \Engine\Book\Search::TYPE_TITLE => $title,
                \Engine\Book\Search::TYPE_ISBN => $isbn
            );
            $bookList = $rep->searchList($attr);
            if (sizeof($bookList) < \Engine\Book\Search::SEARCH_LIMIT) {
            //    $bookList = $search->findBy($attr);
                $bookList = $search->searchBy($attr, $type);
            }
        } else {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_400'));
        }

        return $bookList;
    }

    /**
     * Restore book in the list
     *
     * @param integer $id
     * @return \Data\Doctrine\Main\BookRead
     * @throws \Error\Validation
     */
    public function restoreBookRead($id)
    {
        $em = \System\Registry::connection();
        /* @var $bookRead \Data\Doctrine\Main\BookRead */
        $bookRead = $em->find(CrMain::BOOK_READ, $id);
        if ($bookRead && $bookRead->getUser() === \System\Registry::user()->getEntity()) {
            $bookRead->setStatus(\Defines\Database\BookCategory::READ);
            $em->persist($bookRead);
            $em->flush();
        } else {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_400'), \Defines\Response\Code::E_BAD_REQUEST);
        }
        return $bookRead;
    }

    public function removeBookRead($id)
    {
        $em = \System\Registry::connection();
        /* @var $bookRead \Data\Doctrine\Main\BookRead */
        $bookRead = $em->getRepository(\Defines\Database\CrMain::BOOK_READ)->findOneBy(array(
            'book' => $em->getReference(CrMain::BOOK, $id),
            'user' => \System\Registry::user()->getEntity()
        ));
        if ($bookRead) {
            /* @var $stat \Data\Doctrine\Main\ContentViews */
            $stat = $em->getRepository(CrMain::CONTENT)->getPages($bookRead->getContent()->getPattern());
            $stat->setContentCount($stat->getContentCount() - 1);
            $em->persist($stat);
            $em->remove($bookRead);
            $em->flush();
        } else {
            throw new \Error\Validation(
                \Defines\Response\Code::getHeader(\Defines\Response\Code::E_BAD_REQUEST, null),
                \Defines\Response\Code::E_BAD_REQUEST
            );
        }
    }

    public function move2List($idList, $params)
    {
        $em = \System\Registry::connection();
        $user = \System\Registry::user()->getEntity();
        $oTranslate = \System\Registry::translation();
        // Get list entity
        $list = $em->find(CrMain::CONTENT, $idList);
        if (!$list) {
            throw new \Error\Validation($oTranslate->sys('LB_HEADER_400'));
        }

        /* @var $book \Data\Doctrine\Main\Book */
        $book = $em->find(CrMain::BOOK, $params['isbn']);
        if (!$book) {
            $bookLang = $oTranslate->getTargetLanguage();
            if (array_key_exists('language', $params)) {
                $bookLang = $params['language'];
            }
            $book = $em->getRepository(CrMain::BOOK)->createBook($params['isbn'], $bookLang);
        }

        // Check if the book is in a list
        /* @var $read \Data\Doctrine\Main\BookRead */
        $read = $em->getRepository(CrMain::BOOK_READ)->findOneBy(array(
            //    'content' => $list,
            'book' => $book,
            'user' => $user
        ));
        if ($read && $read->getContent() !== $list) {
            throw new \Error\Validation(
                sprintf($oTranslate->sys('LB_ERROR_BOOK_LIST'),
                    '<a href="' . $read->getContent()->getPattern() . '">'
                    .  $read->getContent()->getContent()
                    . '</a>',
                    \Defines\Database\BookCategory::getTitle($read->getStatus())
                ),
                \Defines\Response\Code::E_MANY_REQUESTS
            );
        }
        if (!$read) {
            $read = new \Data\Doctrine\Main\BookRead();
            $read->setContent($list)
                ->setUser($user)
                ->setBook($book)
                ->setPage(0);
            /* @var $num \Data\Doctrine\Main\ContentViews */
            $num = $em->find(CrMain::CONTENT_VIEWS, $idList);
            $num->setContentCount($num->getContentCount() + 1);
            $em->persist($num);
            // Set to final page (if it's not a new book)
        } elseif ($params['type'] == \Defines\Database\BookCategory::FINISH) {
            $this->deltaPage($read, $book->getPages());
        }
        $read->setStatus($params['type'])
            ->setQueue($params['pos'])
            ->setUpdatedAt(new \DateTime);
        $em->persist($read);
        $em->flush();
    }

    /**
     * Get read list
     *
     * @param integer $idList
     * @param integer $status
     * @param boolean $isCount
     * @return array<\Data\Doctrine\Main\BookRead>
     */
    public function getList($idList, $status = null, $isCount = false)
    {
        $em = \System\Registry::connection();

        $search = array(
            'content' => $em->find(CrMain::CONTENT, $idList)
        );
        $orderBy = array(
            'status' => 'ASC',
            'queue' => 'ASC'
        );
        if (!is_null($status)) {
            $search['status'] = $status;
        }

        $persister = $em->getUnitOfWork()->getEntityPersister(CrMain::BOOK_READ);
        return $isCount ? $persister->count($search) : $persister->loadAll($search, $orderBy, null, null);
    }

    /**
     * Get statistics
     *
     * @param \Data\Doctrine\Main\Content $list
     * @return array
     */
    public function getStatistics($list)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(CrMain::BOOK_READ_HISTORY_DAILY);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria->expr()->eq('content', $list));
        $criteria->andWhere($criteria->expr()->gt('updatedAt', new \DateTime('-1 month')));
        $criteria->orderBy(['bookRead' => 'ASC', 'updatedAt' => 'ASC']);

        $result = array();
        $speed = array(
            'sum' => 0,
            'min' => 0,
            'max' => 0
        );
        /* @var $entity \Data\Doctrine\Main\BookReadHistoryDaily */
        foreach ($rep->matching($criteria) as $entity) {
            $id = $entity->getBookRead()->getId();
            if (!isset($result[$id])) {
                $result[$id] = array(
                    'name' => $entity->getBookRead()->getBook()->getTitle(),
                    'data' => array()
                );
            }
            $time = $entity->getUpdatedAt()->getTimestamp() * 1000;
            $result[$id]['data'][] = array(
                'y' => $entity->getPage(),
                'x' => $time
            );
            if (!$speed['min'] || $time < $speed['min']) {
                $speed['min'] = $time;
            } elseif ($time > $speed['max']) {
                $speed['max'] = $time;
            }
            $speed['sum'] += $entity->getPage();
        }
        // Line
        $val = number_format((1 + $speed['sum']) / (1 + ($speed['max'] - $speed['min']) / (24 * 60 * 60 * 1000) ), 1);
        $result[-1] = array(
            'label' => [
                'text' => sprintf(\System\Registry::translation()->sys('LB_STAT_SPEED'), $val),
                'style' => ['color' => '#6a6351']
            ],
            'width' => 2,
            'color' => '#6a6351',
            'dashStyle' => 'solid',
            'zIndex' => 5,
            'value' => $val
        );
        return $result;
    }

    public function setPage($idList, $isbn, $page)
    {
        $em = \System\Registry::connection();
        /* @var $book \Data\Doctrine\Main\Book */
        $book = $em->find(CrMain::BOOK, $isbn);

        if ($page > $book->getPages()) {
            $page = $book->getPages();
        } elseif ($page < 0) {
            $page = 0;
        }

        /* @var $entity \Data\Doctrine\Main\BookRead */
        $entity = $em->getRepository(CrMain::BOOK_READ)->findOneBy([
            'content' => $em->getReference(CrMain::CONTENT, $idList),
            'book' => $book,
            'user' => \System\Registry::user()->getEntity()
        ]);
        $page && $this->deltaPage($entity, $page);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Save delta for the history
     * @param \Data\Doctrine\Main\BookRead $bookRead
     * @param integer $page
     */
    protected function deltaPage($bookRead, $page)
    {
        $iDelta = $page - $bookRead->getPage();
        if (!$iDelta) {
            return;
        }

        $em = \System\Registry::connection();
        $delta = new \Data\Doctrine\Main\BookReadHistory();
        $delta->setBookRead($bookRead)
            ->setContent($bookRead->getContent())
            ->setPage($iDelta)
            ->setUpdatedAt(new \DateTime);
        $em->persist($delta);
        $em->flush();

        $bookRead->setPage($page);
    }

    public function getReadingSpeed()
    {
        $user = \System\Registry::user()->getEntity();
        return \System\Registry::connection()->getRepository(CrMain::BOOK_READ)->checkUserSpeed($user);
    }
}
