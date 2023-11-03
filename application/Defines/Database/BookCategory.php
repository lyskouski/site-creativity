<?php namespace Defines\Database;

/**
 * Description of BookList
 *
 * @author s.lyskovski
 */
class BookCategory
{

    const WISHLIST = 0;
    const READ = 1;
    const DELETE = 8;
    const FINISH = 9;

    public static function getTitle($status)
    {
        $oTranslate = \System\Registry::translation();
        switch ($status) {
            case self::WISHLIST:
                $status = $oTranslate->sys('LB_BOOK_LIST_WISH');
                break;
            case self::READ:
                $status = $oTranslate->sys('LB_BOOK_LIST_READ');
                break;
            case self::FINISH:
                $status = $oTranslate->sys('LB_BOOK_LIST_FINISH');
                break;
            case self::DELETE:
                $status = $oTranslate->sys('LB_BOOK_LIST_DELETE');
                break;
        }
        return $status;
    }

    public static function getIcon($status)
    {
        switch ($status) {
            case self::WISHLIST:
                $status = '&star;';
                break;
            case self::READ:
                $status = '&orarr;';
                break;
            case self::FINISH:
                $status = '&check;';
                break;
            case self::DELETE:
                $status = '&cross;';
                break;
        }
        return $status;
    }
}
