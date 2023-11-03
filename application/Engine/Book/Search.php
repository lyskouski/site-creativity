<?php namespace Engine\Book;

/**
 * Book Search functionality
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 */
class Search
{

    const TYPE_ISBN = 'isbn';
    const TYPE_TITLE = 'title';
    const TYPE_AUTHOR = 'author';
    const SEARCH_LIMIT = 8;

    /**
     * @see \Defines\Language::getList()
     * @var string
     */
    protected $language;

    /**
     * Define language for a Search Engine
     * @param string $lang
     */
    public function __construct($lang = null)
    {
        if (!$lang) {
            $lang = \System\Registry::translation()->getTargetLanguage();
        }
        $this->language = $lang;
    }

    /**
     * Get Search Engine
     *
     * @return array<\Engine\Book\Search\HelperInterface>
     */
    public function getEngine()
    {
        $list = array();

        switch ($this->language) {
            case \Defines\Language::RU:
            case \Defines\Language::UK:
                // $list[] = new Search\BookRadar(); <- temporary unavailable
                // $list[] = new Search\Rsl($this->language); <- temporary unavailable
                // $list[] = new Search\BasNet($this->language); <- IP restriction
                break;

            case \Defines\Language::BE:
                $list[] = new Search\Kamunikat();
                // $list[] = new Search\BasNet($this->language); <- IP restriction
                // $list[] = new Search\Rsl($this->language); <- temporary unavailable
                break;

            //case \Defines\Language::DE:
            //case \Defines\Language::EN:
            //case \Defines\Language::FR:
            default:
                $list[] = new Search\Amazon();
                break;
        }

        // Check European Library at the end
        // @fixme: $list[] = new Search\EuropeanLibrary($this->language);

        // Add Google Book Search API as a last chance to find the book
        $list[] = new Search\Google();

        /** @fixme @note - for testing only */
        // $list = [new Search\NlbBy($this->language)];

        return $list;
    }

    /**
     * Find book through all the available Book/Search API
     *
     * @param array $search
     * @return \Engine\Book\Result\BookList
     */
    protected function findByEngine($search)
    {
        $bookList = new Result\BookList([]);
        foreach ($this->getEngine() as $engine) {
            $bookList = $engine->fill($bookList, $search);
            if (sizeof($bookList->getArrayCopy())) {
                break;
            }
        }
        return $bookList;
    }

    /**
     * Find by ISBN
     *
     * @param string $isbn
     * @return \Engine\Book\Result\BookList
     */
    public function find($isbn)
    {
        $bookList = $this->findByEngine([self::TYPE_ISBN => $isbn]);
        if (!sizeof($bookList)) {
            $api = new \Engine\Book\Isbn(ltrim($isbn, '0') . 'X');
            $bookList = $this->findByEngine([self::TYPE_ISBN => $api->getIsbn()]);
            if (!sizeof($bookList)) {
                $bookList = $this->findByEngine([self::TYPE_ISBN => $api->getConvertedIsbn()]);
            }
        }
        return $bookList;
    }

    /**
     * Find by author and title
     *
     * @param array $params
     * @return array
     */
    public function findBy($params)
    {
        return $this->findByEngine(array_values($params));
    }

    /**
     * Find by arguments in specific engine
     *
     * @param array $params
     * @param string $engineName
     * @return array
     */
    public function searchBy($params, $engineName)
    {
        $bookList = new Result\BookList([]);
        $engineClass = 'Engine\\Book\\Search\\' . $engineName;
        $engine = new $engineClass();
        return $engine->fill($bookList, $params);
    }
}
