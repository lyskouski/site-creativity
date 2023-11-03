<?php namespace Engine\Book\Search;

/**
 * BookRadar API helper
 * @todo
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search
 */
class BookRadar extends SearchAbstract
{
    protected $path = 'http://bookradar.org/search/ajax/?q=%s&type=all';

    /**
     * Fill values by using BookRadar{emulated} API
     *
     * @param \DOMDocument $result
     * @param \Engine\Book\Result\BookList $bookList
     * @return \Engine\Book\Result\BookList
     */
    public function prepare($result, \Engine\Book\Result\BookList $bookList)
    {
        $finder = new \DomXPath($result);
        /* @var $nodes \DOMNodeList */
        $nodes = $finder->query("//*[contains(@class, 'b-result')]");
        foreach ($nodes as $node) {
            $new = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $new->appendChild($new->importNode($node, true));

            $search = new \DomXPath($new);
            $isbnSearch = $search->query("//*[contains(@class, 'b-result__isbn')]");
            if (!$isbnSearch->length || !$search->query("//*[contains(@class, 'b-result__author')]")->length) {
                continue;
            }
            if (!$this->isbn) {
                $this->isbn = (string) (int) trim(str_replace('ISBN:', '', $isbnSearch->item(0)->nodeValue));
            }
            if (!$this->isbn || strpos($isbnSearch->item(0)->nodeValue, 'X')) {
                continue;
            }
            /* @var $book \Engine\Book\Result\Book */
            $book = $bookList[$this->isbn];

            $img = $new->getElementsByTagName('img');
            if ($img->length) {
                $book->setImage((string) $img->item(0)->attributes->getNamedItem('src')->nodeValue);
            }

            $book->setIsbn($this->isbn)
                ->setAuthor($this->getNodeValue($search->query("//*[contains(@class, 'b-result__author')]")))
                ->setDescription($this->getNodeValue($search->query("//*[contains(@class, 'b-result__desc__short')]")))
                ->setText($this->getNodeValue($search->query("//*[contains(@class, 'b-result__desc__full')]")))
                ->setTitle($this->getNodeValue($search->query("//*[contains(@class, 'b-result__name')]")));

            $subData = explode(';', $this->getNodeValue($search->query("//*[contains(@class, 'b-result__years')]")));
            foreach ($subData as $data) {
                $a = explode(':', $data);
                switch (trim($a[0])) {
                    case 'Год': $book->setDate((int)  trim($a[1])); break;
                    case 'Страниц': $book->setPageCount((int) trim($a[1])); break;
                }
            }
            $bookList[$this->isbn] = $book;
            $this->isbn = '';
        }
        return $bookList;
    }

    protected function getNodeValue($query)
    {
        $value = '';
        if ($query && $query->item(0)) {
            $value = $query->item(0)->nodeValue;
        }
        return trim($value);
    }

    protected function submit($search)
    {
        $content = (new \Engine\Request\Page\Basic)->get(sprintf($this->path, urlencode(trim($search))));
        $doc = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
        $doc->loadHTML("<!DOCTYPE html><html><head><meta content=\"text/html; charset=utf-8\" http-equiv=\"content-type\" /></head><body>$content</body></html>");
        return $doc;
    }

    public function isbn($isbn)
    {
        return $this->submit($isbn);
    }

    public function search($author, $title)
    {
        $search = '';
        if ($author) {
            $search .= $author;
        }
        if ($title) {
            $search .= ' ' . $title;
        }
        return $this->submit($search);
    }
}
