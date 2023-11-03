<?php namespace Modules\Book\Overview;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\Oeuvre\Model
{

    protected $targetUrl = 'book/overview';

    public function getBook($id)
    {
        /* @var $book \Data\Doctrine\Main\Content */
        $book = $this->em->find(\Defines\Database\CrMain::CONTENT, $id);
        if (!$book) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_410'),
                \Defines\Response\Code::E_DELETED
            );
        }

        $oTranslate = \System\Registry::translation();
        return $oTranslate->entity(['og:title', $book->getPattern()]);
        //$isbn = ltrim($oTranslate->entity(['isbn', $book->getPattern()])->getContent(), '0');
        //return $this->em->find(\Defines\Database\CrMain::BOOK, $isbn)->getContent();
        // return $book;
    }

    public function checkBookRead($book)
    {
        $oTranslate = \System\Registry::translation();
        $isbn = $oTranslate->entity(['isbn', $book->getPattern()])->getContent();
        return $this->em->getRepository(\Defines\Database\CrMain::BOOK_READ)->findBy([
            'user' => \System\Registry::user()->getEntity(),
            'book' => $this->em->getReference(\Defines\Database\CrMain::BOOK, $isbn)
        ]);
    }

    public function getBookInfo($id)
    {
        $book = $this->getBook($id);

        // @todo change to content_view
        $num = $this->em->createQuery(
            'SELECT SUM(c.content)/COUNT(c.id) AS mark, COUNT(c.id) AS cnt
            FROM Data\Doctrine\Main\Content c
            WHERE c.type LIKE :type AND c.pattern = :pattern')
            ->setParameter('type', 'mark-%')
            ->setParameter('pattern', $book->getPattern())
            ->getSingleResult();

        // List of related books
        $seriesRep = $this->em->getRepository(\Defines\Database\CrMain::CONTENT_SERIES);

        $result = array(
            'entity' => $book,
            'mark' => $num['mark'],
            'cnt' => $num['cnt'],
            'comments' => $this->rep->findComments($book->getPattern(), 0, 3),
            'series' => $seriesRep->findByContent($book)
        );

        if (\System\Registry::user()->isLogged()) {
            $result['list'] = (new \Modules\Book\Calendar\Model)->getAllList(false);
            $result['read'] = $this->checkBookRead($book);
        }

        return $result;
    }

    /**
     * @param \Data\Doctrine\Main\Content $book
     */
    public function getBookQuotes($book, $id = null)
    {
        $em = \System\Registry::connection();

        $condition = array(
            'type' => 'quote',
            'content2' => [$book, $this->em->getReference(\Defines\Database\CrMain::CONTENT, $id)],
            'language' => $book->getLanguage(),
            'access' => \Defines\User\Access::getModApprove()
        );

        if (\System\Registry::user()->isLogged() && $this->checkBookRead($book)) {
            unset($condition['access']);
        }

        $list = $em->getRepository(\Defines\Database\CrMain::CONTENT)
                ->findBy($condition, ['updatedAt' => 'DESC']);

        usort($list, function($a, $b) use ($book) {
            $k = strlen($book->getPattern()) + 1;
            $pgA = (int) substr($a->getPattern(), $k);
            $pgB = (int) substr($b->getPattern(), $k);
            return $pgA - $pgB;
        });

        return $list;
    }

    /**
     * Get summary statistic information
     *
     * @return \Data\Doctrine\Main\ContentViews
     */
    public function getStatistics()
    {
        return $this->rep->getPages($this->targetUrl);
    }

    /**
     * Get publications on the current page
     *
     * @param integer $iPage
     * @return array
     */
    public function getPublications($iPage = 0)
    {
        return $this->rep->findLastTopics($this->targetUrl, $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByKey($sKey, $iPage = 0) {
        return $this->rep->findTopicsByKey($this->targetUrl, $sKey, $this->iLimit * $iPage, $this->iLimit);
    }

    public function getPublicationsByPart($sKey, $iPage = 0) {
        $result = $this->rep->findTopicsByKey($this->targetUrl, $sKey, $this->iLimit * $iPage, $this->iLimit, ['keywords', 'og:title', 'description']);
        if ($result['count']) {
            $this->autocreateInfo($this->targetUrl, $sKey);
        }
        return $result;
    }

    public function getPublicationsByAuthor($sKey, $iPage = 0)
    {
        $result = $this->rep->findTopicsByKey($this->targetUrl, $sKey, $this->iLimit * $iPage, $this->iLimit, ['author']);
        if ($result['count']) {
            $this->autocreateInfo($this->targetUrl, $sKey, 'author');
        }
        return $result;
    }

}
