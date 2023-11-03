<?php namespace Engine\Book\Search;

/**
 * KnigaFund API helper
 * @todo
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 */
class KnigaFund implements SearchInterface
{

    protected $api;

    public function __construct()
    {
        $config = \System\Registry::config();
        $this->api = $config->getSocialApi('knigafund');
    }

    /**
     * Fill values by using KnigaFund API
     *
     * @param \Engine\Book\Result\BookList $bookList
     * @param array $params
     * @return \Engine\Book\Result\BookList
     */
    public function fill(\Engine\Book\Result\BookList $bookList, array $params)
    {
        $isbn = '';
        if (array_key_exists(\Engine\Book\Search::TYPE_ISBN, $params)) {
            $isbn = $params[\Engine\Book\Search::TYPE_ISBN];
            $json = $this->isbn($isbn);
        } else {
            list($author, $title) = $params;
            $json = $this->search($author, $title);
        }
    }

    public function search($author, $title)
    {
        ;
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
            file_get_contents("http://api.knigafund.ru/api/books.json?api_key={$this->api['key']}"),
            true
        );
    }
}
