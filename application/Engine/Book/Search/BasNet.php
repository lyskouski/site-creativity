<?php namespace Engine\Book\Search;

/**
 * Amazon API helper
 * @link http://libcat.bas-net.by/belmova
 * @sample http://libcat.bas-net.by/belmova/pls/pages.view_doc?off=0&siz=10&qid=58802&format=full&nn=1
 *
 * @sample ISBN: 9789859025471, 9789854768250
 *
 * @todo - forbidden
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search
 */
class BasNet extends SearchAbstract
{

    protected $lang;
    protected $url;
    protected $bookLang;
    protected $walker;

    /**
     * Init language params
     * @param string $language
     */
    public function __construct($language = \Defines\Language::BE)
    {
        $this->walker = new \Engine\Request\Page\Walker();
        $this->bookLang = $language;
        switch ($language) {
            case \Defines\Language::BE:
                $this->lang = 'bel';// http://libcat.bas-net.by/belmova
                break;
            case \Defines\Language::DE:
                $this->lang = 'ger';
                break;
            case \Defines\Language::EN:
                $this->lang = 'eng';
                break;
            case \Defines\Language::FR:
                $this->lang = 'fre';
                break;
            case \Defines\Language::RU:
                $this->lang = 'rus';
                break;
            case \Defines\Language::UK:
                $this->lang = 'ukr';
                break;
        }
    }

    /**
     * @param \DOMDocument $result
     * @param \Engine\Book\Result\BookList $bookList
     */
    protected function prepare($result, \Engine\Book\Result\BookList $bookList)
    {
        $finder = new \DomXPath($result);
        /* @var $form \DOMNodeList */
        $form = $finder->query("//form[contains(@action, 'pages.view_doc')]");
        if ($form->length) {
            $new = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $new->appendChild($new->importNode($form->item(0), true));
            $search = new \DomXPath($new);
            $qid = (string) $search->query("//input[contains(@name, 'qid')]")->item(0)->attributes->getNamedItem('value')->nodeValue;
            /* @var $nn \DOMNodeList */
            $nn = $search->query("//input[contains(@name, 'nn')]");
            foreach ($nn as $node) {
                $url = 'http://libcat.bas-net.by/opac/pls/pages.view_doc?' . http_build_query(array(
                    'format' => 'full',
                    'nn' => (int) $node->attributes->getNamedItem('value')->nodeValue,
                    'off' => 0,
                    'siz' => 10,
                    'qid' => $qid
                ));
                /* @var $book \Engine\Book\Result\Book */
                $book = $this->parseDocument($url);
                $bookList[$book->getIsbn()]->setAuthor($book->getAuthor())
                    ->setTitle($book->getTitle())
                    ->setCategory($book->getCategory())
                    ->setDate($book->getDate())
                    ->setDescription($book->getDescription())
                    ->setIsbn($book->getIsbn())
                    ->setLanguage($book->getLanguage())
                    ->setPageCount($book->getPageCount())
                    ->setUdc($book->getUdc());
            }
        }
        return $bookList;
    }

    public function isbn($isbn)
    {
        $oIsbn = new \Engine\Book\Isbn($isbn);
        // formatted ISBN is used
        return $this->getDocument("http://libcat.bas-net.by/opac/pls/!search.http_extended?qoption=0&qtext={$oIsbn->getIsbn()}&qkey=isbn&lst_siz=10");
    }

    public function search($author, $title)
    {
        $url = 'http://libcat.bas-net.by/opac/pls/!search.http_extended?' . http_build_query(array(
            'd1' => '',
            'd2'	 => '',
            'lang' => $this->lang,
            'lst_siz' => 10,
            'qbool1' => 1,
            'qbool2' => 1,
            'qbool3' => 1,
            'qkey' => array('title', 'psn', 'subj', 'cont'),
            'qoption' => array(2, 2, 1, 0),
            'qtext' => array($title, $author, '', ''),
            'union_val' => 'mrk19=b and mrk7=m'
        ));
        return $this->getDocument($url);
    }

    /**
     * Get Document entity by URL
     *
     * @param string $url
     * @return \DOMDocument
     */
    protected function getDocument($url)
    {
        $content = str_replace(' id="', ' class="', $this->walker->getContent($url));
        $doc = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
        $html = (new \System\Converter\Helper\Html)->repair($content);
        $doc->loadHTML("<!DOCTYPE html><html><head><meta content=\"text/html; charset=utf-8\" http-equiv=\"content-type\" /></head><body>{$html}</body></html>");
        return $doc;
    }

    /**
     * Parse list of values
     *
     * @param \DOMNode $dom
     * @param string $sep
     * @return string
     */
    protected function parseList($dom, $sep = ', ')
    {
        $a = array();
        foreach ($dom->childNodes->item(2)->childNodes as $node) {
            $value = trim($node->nodeValue);
            //if (strpos($value, '(')) {
            //    $value = trim(substr($value, 0, strpos($value, '(')));
            //}
            if ($value) {
                $a[] = $value;
            }
        }
        return implode($sep, $a);
    }

    /**
     * Prepare book info
     *
     * @param string $url
     * @return \Engine\Book\Result\Book
     */
    protected function parseDocument($url)
    {
        $book = new \Engine\Book\Result\Book();
        $book->setLanguage($this->bookLang);

        $bookHtml = $this->getDocument($url);
        $finder = new \DomXPath($bookHtml);
        $node = $finder->query("//table[contains(@class, 'full')]")->item(0);
        $new = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
        $new->appendChild($new->importNode($node, true));
        /* @var $element \DOMNode */
        foreach ($new->getElementsByTagName('tr') as $element) {
            $type = trim($element->firstChild->nodeValue);
            $value = $element->childNodes->item(2)->nodeValue;
            switch ($type) {
                case 'ISBN:':
                    $book->setIsbn(str_replace('-', '', filter_var($value, FILTER_SANITIZE_NUMBER_INT)));
                    break;
                case 'Заглавие:':
                    $book->setTitle($value);
                    break;
                case 'Авторы:':
                case 'Ответственные лица:':
                    $book->setAuthor($this->parseList($element));
                    break;
                case 'Физич.характеристики:':
                    $book->setPageCount((int) $value);
                    break;
                case 'Тематика:':
                    $book->setCategory($this->parseList($element));
                    break;
                case 'Издано в:':
                    $book->setDate(substr($value, strrpos($value, ', ')+2));
                    break;
                case 'УДК:':
                    $book->setUdc($this->parseList($element, '; '));
                    break;
                case 'Примечания:':
                    $book->setDescription($value);
                    break;

            }
        }
        return $book;
    }
}
