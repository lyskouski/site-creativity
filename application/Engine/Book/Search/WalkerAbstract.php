<?php namespace Engine\Book\Search;

/**
 * Description of WalkerAbstract
 *
 * @author s.lyskovski
 */
abstract class WalkerAbstract extends SearchAbstract
{
    const SEARCH_NONE = 0;
    const SEARCH_ISBN = 1;
    const SEARCH_TITLE = 3;
    const SEARCH_AUTHOR = 2;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var \Engine\Request\Page\Walker
     */
    protected $walker;

    public function __construct($language = null)
    {
        $this->language = $language;
        $this->walker = new \Engine\Request\Page\Walker();
    }

    /**
     * @return mixed
     */
    abstract protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = null, $val3 = '', $key3 = null);

    /**
     * @param mixed $content - result of search
     * @return array<\Engine\Book\Result\Book>
     */
    abstract protected function prepareResult($content);


    protected function getNode($query, $num = 0, $attr = false)
    {
        $value = '';
        if ($query && $query->item($num)) {
            if ($attr) {
                $value = $query->item($num)->attributes->getNamedItem($attr)->nodeValue;
            } else {
                $value = $query->item($num)->nodeValue;
            }
        }
        return trim($value);
    }

    public function isbn($isbn)
    {
        // Language is not needed for ISBN search
        $this->language = '';

        $oIsbn = new \Engine\Book\Isbn($isbn);
        $content = $this->bindSearchForm($oIsbn->getIsbn(), static::SEARCH_ISBN);
        return $this->prepareResult($content);
    }

    public function search($author, $title)
    {
        if ($author && !$title) {
            $content = $this->bindSearchForm($author, static::SEARCH_AUTHOR);
        } elseif (!$author && $title) {
            $content = $this->bindSearchForm($title, static::SEARCH_TITLE);
        } else {
            $content = $this->bindSearchForm($author, static::SEARCH_AUTHOR, $title, static::SEARCH_TITLE);
        }
        return $this->prepareResult($content);
    }

    protected function prepare($result, \Engine\Book\Result\BookList $bookList)
    {
        /* @var $book \Engine\Book\Result\Book */
        foreach ($result as $book) {
            if (!$book->getIsbn()) {
                continue;
            }
            $bookList[$book->getIsbn()]->setAuthor($book->getAuthor())
                ->setImage($book->getImage())
                ->setTitle($book->getTitle())
                ->setCategory($book->getCategory())
                ->setDate($book->getDate())
                ->setDescription($book->getDescription())
                ->setIsbn($book->getIsbn())
                ->setLanguage($book->getLanguage())
                ->setPageCount($book->getPageCount())
                ->setUdc($book->getUdc())
                ->setText($book->getText());
        }
        return $bookList;
    }
}
