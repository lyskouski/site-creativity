<?php namespace Engine\Book\Search;

/**
 * API helper
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 */
interface SearchInterface
{

    /**
     * Fill values by using API
     *
     * @param \Engine\Book\Result\BookList $bookList
     * @param array $params
     * @return \Engine\Book\Result\BookList
     */
    public function fill(\Engine\Book\Result\BookList $bookList, array $params);

    /**
     * Use API to find books by ISBN
     *
     * @param string $isbn
     * @return mixed
     */
    public function isbn($isbn);

    /**
     * Use API to find books by author/title
     *
     * @param string $author
     * @param string $title
     * @return mixed
     */
    public function search($author, $title);
}
