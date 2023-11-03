<?php namespace Defines\Response;

use Access\Request\Search;

/**
 * Description of ListType
 *
 * @author s.lyskovski
 */
class ListType
{
    public static function getType()
    {
        $request = new \Engine\Request\Input();
        if ($request->getPost(Search::SPLIT)) {
            $request->setCookie(Search::SPLIT, $request->getPost(Search::SPLIT, Search::SPLIT_TILE));
        }

        $result = 'Basic/notion';
        if ($request->getCookie(Search::SPLIT) === Search::SPLIT_PLAIN) {
            $result = 'Basic/notion_plain';
        }
        return $result;
    }
}
