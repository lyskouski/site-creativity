<?php namespace Engine\Book\Search;

/**
 * Google API helper
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search
 */
class Google extends SearchAbstract
{

    /**
     * @var \Google_Service_Books
     */
    protected $api;

    /**
     * Init Amazon API connection
     * @return ApaiIO
     */
    public function __construct()
    {
        $config = \System\Registry::config();
        $apiGoogle = $config->getSocialApi('google');
        $auth = $config->getConfigPath() . $apiGoogle['json'];

        $client = new \Google_Client();
        $client->setAuthConfig($auth);
        $client->setScopes(array(
            \Google_Service_Books::BOOKS
        ));

        $this->api = new \Google_Service_Books($client);
    }

    /**
     * Fill values by using Google API
     *
     * @param \Google_Service_Books_Volume $result
     * @param \Engine\Book\Result\BookList $bookList
     * @return \Engine\Book\Result\BookList
     */
    public function prepare($result, \Engine\Book\Result\BookList $bookList)
    {
        $i = 0;
        foreach ($result->getItems() as $i => $item) {
            $i++;
            $isbn = '';
            $volumeInfo = $item['modelData']['volumeInfo'];
            if (!isset($volumeInfo['industryIdentifiers'])) {
                continue;
            }
            foreach ($volumeInfo['industryIdentifiers'] as $tp) {
                if (strtolower($tp['type']) === 'isbn_13' || strtolower($tp['type']) === 'isbn') {
                    $isbn = (string) $tp['identifier'];
                    break;
                }
            }
            if (!$isbn) {
                continue;
            }
            /* @var $book \Engine\Book\Result\Book */
            $book = $bookList[$isbn];
            $book->setIsbn($isbn);
            $book->setTitle($volumeInfo['title']);
            if (array_key_exists('subtitle', $volumeInfo)) {
                $book->setDescription($volumeInfo['subtitle']);
            }
            if (array_key_exists('description', $volumeInfo)) {
                $book->setDescription($volumeInfo['description']);
            }
            if (array_key_exists('authors', $volumeInfo) && (!$book->getAuthor() || sizeof($volumeInfo['authors']))) {
               $book->setAuthor(implode(', ', $volumeInfo['authors']));
            }
            if (array_key_exists('publishedDate', $volumeInfo)) {
                $book->setDate($volumeInfo['publishedDate']);
            }
            if (array_key_exists('pageCount', $volumeInfo)) {
                $book->setPageCount($volumeInfo['pageCount']);
            }
            if (array_key_exists('imageLinks', $volumeInfo)) {
                $book->setImage($volumeInfo['imageLinks']['thumbnail']);
            }
            if (array_key_exists('language', $volumeInfo)) {
                $book->setLanguage($volumeInfo['language']);
            }
            $bookList[$isbn] = $book;
            if ($i > \Engine\Book\Search::SEARCH_LIMIT) {
                break;
            }
        }
        return $bookList;
    }

    /**
     * Exec Book Google API
     *
     * @param string $isbn
     * @return \SimpleXMLElement
     */
    public function isbn($isbn)
    {
        $optParams = array(
        //    'langRestrict' => \System\Registry::translation()->getTargetLanguage()
        );
        return $this->api->volumes->listVolumes("isbn:$isbn", $optParams);
    }

    /**
     * Exec Book Google API
     *
     * @param string $author
     * @param string $title
     * @return \SimpleXMLElement
     */
    public function search($author, $title)
    {
        $optParams = array(
        //    'langRestrict' => \System\Registry::translation()->getTargetLanguage()
        );
        $search = array();
        if ($author) {
            $search[] = '+inauthor:'.urlencode($author);
        }
        if ($title) {
            $search[] = '+intitle:'.urlencode($title);
        }
        return $this->api->volumes->listVolumes(implode('', $search), $optParams);
    }
}
