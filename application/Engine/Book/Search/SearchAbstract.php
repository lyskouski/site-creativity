<?php namespace Engine\Book\Search;

use Engine\Book\Result\BookList;

/**
 * API helper
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 */
abstract class SearchAbstract implements SearchInterface
{
    /**
     * @var string - ISBN identificator
     */
    protected $isbn;

   /**
     * Fill values by using Amazon API
     *
     * @param BookList $bookList
     * @param array $params
     * @return BookList
     */
    public function fill(BookList $bookList, array $params)
    {
        $this->isbn = '';
        if (array_key_exists(\Engine\Book\Search::TYPE_ISBN, $params) && $params[\Engine\Book\Search::TYPE_ISBN]) {
            $this->isbn = (string) $params[\Engine\Book\Search::TYPE_ISBN];
            $result = $this->isbn($this->isbn);
        } elseif (array_key_exists(\Engine\Book\Search::TYPE_AUTHOR, $params)) {
            $author = $params[\Engine\Book\Search::TYPE_AUTHOR];
            $title = $params[\Engine\Book\Search::TYPE_TITLE];
            $result = $this->search($author, $title);
        } else {
            list($author, $title) = $params;
            $result = $this->search($author, $title);
        }
        return $this->prepare($result, $bookList);
    }

    /**
     * Update BookList results
     *
     * @param mixed $result
     * @param \Engine\Book\Result\BookList $bookList
     * @return BookList
     */
    abstract protected function prepare($result, BookList $bookList);
}
