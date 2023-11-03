<?php namespace Engine\Book\Search;

/**
 * ISBNDB API helper
 * @todo
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search
 */
class Isbndb
{

    protected $api;

    public function __construct()
    {
        $config = \System\Registry::config();
        $this->api = $config->getSocialApi('isbndb');
    }

    /**
     * Exec Book ISBNDB API
     *
     * @param string $isbn
     * @return array
     */
    public function isbn($isbn)
    {
        return json_decode(
            (new \Engine\Request\Page\Basic)->get("http://isbndb.com/api/v2/json/{$this->api['key']}/book/{$isbn}"),
            true
        );
    }
}
