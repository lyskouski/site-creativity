<?php namespace Modules\Book\Series;

use Defines\Database\CrMain;

/**
 * Description of Model
 *
 * @author slaw
 */
class Model extends \Modules\Book\Overview\Model
{
    public function __construct()
    {
        parent::__construct();
        $this->targetUrl = 'book/series';
    }

    /**
     *
     * @param type $id
     * @return \Data\Doctrine\Main\Content
     * @throws \Error\Validation
     */
    public function getSeries($id)
    {
        $oTranslate = \System\Registry::translation();

        $series = $this->em->find(CrMain::CONTENT, (int) $id);
        if (!$series) {
            throw new \Error\Validation(
                $oTranslate->sys('LB_HEADER_410'),
                    \Defines\Response\Code::E_DELETED
            );
        }
        return $series;
    }

    /**
     *
     * @param \Data\Doctrine\Main\Content $series
     * @return array<\Data\Doctrine\Main\ContentSeries>
     */
    public function getSeriesContent(\Data\Doctrine\Main\Content $series)
    {
        return $this->em->getRepository(CrMain::CONTENT_SERIES)->findBySeries($series);
    }

    public function checkSeriesRead($seriesList)
    {
        $tmp = array();
        /* @var $o \Data\Doctrine\Main\ContentSeries */
        foreach ((array) $seriesList as $o) {
            $tmp[] = $o->getContent();
        }

        $read = array();
        $tmpRead = $this->em->createQuery(
                    "SELECT br.id AS id, IDENTITY(b.content, 'id') AS content
                    FROM Data\Doctrine\Main\BookRead br
                    INNER JOIN Data\Doctrine\Main\Book b WITH br.book = b.id
                    WHERE b.content IN (:content) AND br.user = :user"
                )->setParameter('user', \System\Registry::user()->getEntity())
                ->setParameter('content', $tmp)
                ->getResult();
        foreach ($tmpRead as $a) {
            $read[$a['content']] = $this->em->find(CrMain::BOOK_READ, $a['id']);
        }
        return $read;
    }

}
