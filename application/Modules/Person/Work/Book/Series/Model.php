<?php namespace Modules\Person\Work\Book\Series;

/**
 * Model to create new book series
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Model extends \Modules\Person\Work\Book\Model
{

    public function getUrl()
    {
        return 'person/work/book/series';
    }

    public function getBookList($url)
    {
        $a = explode('book/calendar/i', $url);
        if (!$a) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_400'),
                \Defines\Response\Code::E_BAD_REQUEST
            );
        }
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::BOOK_READ);
        $list = $rep->findByContent($em->getReference(\Defines\Database\CrMain::CONTENT, $a[1]));
        $isbn = array();
        /* @var $o \Data\Doctrine\Main\BookRead */
        foreach ($list as $o) {
            $isbn[] = $o->getBook()->getId();
        }
        return $isbn;
    }
}
