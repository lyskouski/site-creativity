<?php namespace Modules\Person\Work\Book;

/**
 * Model to create new article
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Model extends \Modules\Person\Work\Article\Model
{

    /**
     * Get initial url for topic
     * @abstract for other classes
     *
     * @return string
     */
    public function getUrl()
    {
        return 'person/work/book';
    }

    /**
     * Get list of categories
     * @abstract for other classes
     *
     * @return array
     */
    public function getTyped()
    {
        return array();
    }

    protected function getFields($aPost)
    {
        $list = array_keys($aPost);
        return array_diff($list, ['height', 'width', 'file', 'action']);
    }

    public function clearContent($key)
    {
        $em = \System\Registry::connection();
        $result = $this->getEntities($key);
        /* @var $o \Data\Doctrine\Main\ContentNew */
        foreach ($result as $o) {
            if (strpos($o->getType(), 'content#') !== false) {
                $em->remove($o);
            }
        }
        $em->flush();
    }

    /**
     * Find book description by using ISBN
     * @sample ISBN: 9785699250493, 1499197519
     *
     * @param string $isbn
     * @return array
     */
    public function findNewBook($isbn, array $params = array())
    {
        if (!$isbn) {
            return $params;
        }
        $lang = \System\Registry::translation()->getTargetLanguage();
        if (array_key_exists('language', $params)) {
            $lang = $params['language'];
        }
        $engine = new \Engine\Book\Search($lang);

        $bookList = $engine->find($isbn);
        /* @var $book \Engine\Book\Result\Book */
        $book = $bookList->current();
        return array_merge($params, array(
            'isbn' => $book->getIsbn(),
            'og:title' => $book->getTitle(),
            'date' => $book->getDate(),
            'udc' => $book->getUdc(),
            'og:image' => $book->getImage(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'content#0' => $book->getText(),
            'keywords' => $book->getCategory(),
            'pageCount' => $book->getPageCount()
        ));
    }

}
