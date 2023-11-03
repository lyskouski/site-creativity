<?php namespace Engine\Book\Search;

/**
 * Search via theeuropeanlibrary.org
 *
 * @since 2016-09-23
 * @author Viachaslau Lyskouski
 */
class EuropeanLibrary extends WalkerAbstract
{

    public function __construct($language = null)
    {
        parent::__construct($language);
        switch ($language) {
            case \Defines\Language::BE:
                $this->language = 'sla';
                break;
            case \Defines\Language::DE:
                $this->language = 'ger';
                break;
            case \Defines\Language::EN:
                $this->language = 'eng';
                break;
            case \Defines\Language::FR:
                $this->language = 'fre';
                break;
            case \Defines\Language::RU:
                $this->language = 'rus';
                break;
            case \Defines\Language::UK:
                $this->language = 'sla';
                break;
        }
        $this->url = 'http://www.theeuropeanlibrary.org';
    }

    public function isbn($isbn)
    {
        $content = $this->bindSearchForm($isbn, static::SEARCH_ISBN);
        return $this->prepareResult($content);
    }


    protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = null, $val3 = '', $key3 = null)
    {
        $search = array();
        for ($i = 1; $i < 4; $i++) {
            $val = trim(${'val'.$i});
            if (!$val) {
                continue;
            }

            switch (${'key'.$i}) {
                case self::SEARCH_ISBN:
                    $term = 'ANY';
                    break;
                case self::SEARCH_AUTHOR:
                    $term = 'CREATOR';
                    break;
                case self::SEARCH_TITLE:
                    $term = 'TITLE';
                    break;

                default:
                    continue 2;
            }
            $search[] = "({$term},{$val})";
        }
        if ($this->language) {
            $search[] = "(LANGUAGE,{$this->language})";
        }

        return $this->walker->getContent($this->url . '/tel4/search?' . http_build_query(['query' => 'advanced(' . implode('AND', $search) . ')']));
    }

    protected function prepareResult($content)
    {
        $dom = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
        $html = (new \System\Converter\Helper\Html)->repair(substr($content, strpos($content, '<!-- AddThis Button BEGIN -->')));

        $dom->loadHTML(
            '<!DOCTYPE html><html><head><meta content="text/html; charset=utf-8" http-equiv="content-type" /></head><body>'.
            str_replace(
                array(
                    'id="first"', 'id="previous"', 'id="next"', 'id="last"',
                    'id="timeline-div-zoom"', 'id="link-europeana"', 'id="link-mendeley"',
                    'id="link-core"'
                ),
                '',
                $html
            )
            . '</body></html>');

        $find = new \DomXPath($dom);
        $list = array();
        for ($i = 0; $i < 20; $i++) {
            $tmp = $find->query("//tr[contains(@class, 'row-{$i}')]");
            if (!$tmp->length) {
                break;
            }
            $new = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $new->appendChild($new->importNode($tmp->item(0), true));
            $url = $new->getElementsByTagName('a');
            if ($url->length) {
                $list[] = $this->prepareBook($this->url . $this->getNode($url, 0, 'href'));
            }
        }
        return $list;
    }

    /**
     * @param string $url
     * @return \Engine\Book\Result\Book
     */
    protected function prepareBook($url)
    {
        $book = new \Engine\Book\Result\Book();

        $bookPage = $this->walker->getContent($url);
        $content = $this->walker->getDomDocument($bookPage);
        foreach ($content->getElementsByTagName('h3') as $item) {
            if (!$item->nextSibling->nextSibling) {
                continue;
            }
            $value = trim($item->nextSibling->nextSibling->nodeValue);
            switch (trim($item->nodeValue)) {
                case 'Harvard-Style Citation':
                    $book->setDescription($value);
                    break;
            //    case 'Creator':
            //        $book->setAuthor($value);
            //        break;
                case 'ISBN':
                    $book->setIsbn($value);
                    break;
                case 'Publication':
                    $book->setDate((int) filter_var($value, FILTER_SANITIZE_NUMBER_INT));
                    break;
                case 'Extent':
                    $book->setPageCount((int) $value);
                    break;
                case 'Collection':
                    $book->setText($value);
                    break;
            }
        }

        $tmpTitle = $this->getValue($bookPage, 'meta name="dc.title" content="');
        if ($tmpTitle) {
            $book->setTitle($tmpTitle);
        }
        $tmpAuth = $this->getValue($bookPage, 'meta name="dc.creator" content="');
        if ($tmpAuth) {
            $book->setAuthor($tmpAuth);
        }
        $tmpDate = $this->getValue($bookPage, 'meta name="dc.date" content="');
        if ($tmpDate) {
            $book->setDate($tmpDate);
        }

        $img = $this->getValue($bookPage, 'id="item-carousel"', '</div>');
        if ($img) {
            $tmp = $this->getValue($img, 'src="');
            if ($tmp) {
                $book->setImage($tmp);
            }
        }

        return $book;
    }

    protected function getValue($content, $start, $end = '"')
    {
        $value = '';
        $key = strpos($content, $start);
        if ($key !== false) {
            $tmp = substr($content, $key + strlen($start));
            $value = substr($tmp, 0, strpos($tmp, $end));
        }
        return $value;
    }
}
