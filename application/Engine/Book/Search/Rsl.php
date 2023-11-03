<?php namespace Engine\Book\Search;

/**
 * Search via rsl.com
 *
 * @since 2016-09-23
 * @author Viachaslau Lyskouski
 */
class Rsl extends WalkerAbstract
{

    public function __construct($language = null)
    {
        parent::__construct($language);
        $this->language = 'ru';
        $this->url = 'https://search.rsl.ru';
    }

    protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = null, $val3 = '', $key3 = null)
    {
        $walker = new \Engine\Request\Page\Walker();
        // Visit page for cookies
        $searchContent = $walker->getContent($this->url . '/' . $this->language . '/search');
        // Prepare data
        $search = array();
        for ($i = 1; $i < 4; $i++) {
            switch (${'key'.$i}) {
                case self::SEARCH_ISBN:
                    $prefix = 'isbn';
                    break;
                    //$search = array(str_replace('-', '', ${'val'.$i}));
                    //break 2;
                case self::SEARCH_AUTHOR:
                    $prefix = 'author';
                    break;
                case self::SEARCH_TITLE:
                    $prefix = 'title';
                    break;

                default:
                    continue 2;
            }
            $term = $prefix . ':(' . ${'val'.$i} . ')';
            $tmp = http_build_query(array(
                'language' => $this->language,
                'dict' => $prefix,
                'term' => $term
            ));
            $walker->getContent($this->url . '/site/ajax-suggest?' . $tmp);
            $search[] = $term;
        }
        // Check get-proposed-docs page
        $walker->getContent($this->url . '/site/get-proposed-docs?language=' . $this->language);
        // Check external/ebsco page
        $walker->getContent($this->url . '/' . $this->language . '/external/ebsco?' . http_build_query(array(
            'q' => implode(' ', $search),
            'c' => 1
        )));
        // Send form
        $params = array(
            'SearchFilterForm[accessFree]' => 0,
            'SearchFilterForm[accessLimited]' => 0,
            'SearchFilterForm[elfunds]' => 0,
            'SearchFilterForm[enterdatefrom]' => '',
            'SearchFilterForm[enterdateto]' => '',
            'SearchFilterForm[fdatefrom]' => '',
            'SearchFilterForm[fdateto]' => '',
            'SearchFilterForm[fulltext]' => 0,
            'SearchFilterForm[nofile]' => 0,
            'SearchFilterForm[page]' => 1,
            'SearchFilterForm[pubyearfrom]' => '',
            'SearchFilterForm[pubyearto]' => '',
            'SearchFilterForm[search]' => implode(' ', $search),
            'SearchFilterForm[sortby]' => 'default',
            'SearchFilterForm[updatedFields][]' => 'search'
        );
        $walker->setHeader(null, 'Referer: ' . $this->url . '/' . $this->language . '/search', true, true);
        $a = explode('name="csrf-token" content="', $searchContent);
        if (sizeof($a) < 2) {
            throw new \Error\Validation('[NLBe] Broken CSRF Token', \Defines\Response\Code::E_FATAL);
        }
        $walker->setHeader(null, 'X-CSRF-Token: ' . substr($a[1], 0, strpos($a[1], '"')), true, true);
        $walker->setHeader(null, 'X-Requested-With: XMLHttpRequest', true, true);
        $walker->setHeader(null, 'Accept: application/json, text/javascript, */*; q=0.01', true, true);
        return $walker->postData($params, $this->url . '/site/ajax-search?language=' . $this->language);
    }

    protected function prepareResult($content)
    {
        $result = array();
        $walker = new \Engine\Request\Page\Walker();

        $list = explode('data-rid="', str_replace('\\', '', $content));

        $num = sizeof($list);
        for ($i = 1; $i < $num; $i++) {
            $id = substr($list[$i], 0, strpos($list[$i], '"'));
            if (array_key_exists($id, $result)) {
                continue;
            }
            $data = $walker->getContent($this->url . '/' . $this->language . '/record/' . $id);
            $result[$id] = $this->parseBook($data);
        }

        return array_values($result);
    }

    /**
     * @param \DOMDocument $content
     * @return \Engine\Book\Result\Book
     */
    protected function parseBook($content)
    {
        $book = new \Engine\Book\Result\Book();

        $img = explode('book-cover', $content);
        if (sizeof($img) > 1) {
            $tmp = substr($img[1], strpos($img[1], 'src="')+5);
            $book->setImage($this->url . substr($tmp, 0, strpos($tmp, '"')));
        }

        $tmp = substr($content, strpos($content, 'id="marc-rec"'));
        $tmp2 = substr($tmp, strpos($tmp, '>') + 1);

        $fields = array_map('strip_tags', preg_split('#<(\/td|\/strong|br\/)>#', substr($tmp2, 0, strpos($tmp2, '</table>'))));
        $key = '';
        foreach ($fields as $i => $line) {
            switch ($line) {
                case '100': $key = 'author'; break;
                case '001':
                case '020': $key = 'isbn';   break;
                case '246':
                case '245': $key = 'title';  break;
                case '260': $key = 'year';   break;
                case '300': $key = 'pages';  break;
                case '856': $key = 'link';   break; /** @todo parse URL for download purposes */
                case '650': $key = 'categ';  break;

            }
            if ($line !== '$a') {
                continue;
            }
            $val = $this->fixValue($fields[$i + 1]);
            // Define parameter
            switch ($key) {
                case 'author':
                    $book->setAuthor($val, false);
                    break;

                case 'year':
                    for ($k = 0; $k < 10; $k++) {
                        if ($fields[$i + $k] === '$c') {
                            break;
                        }
                    }
                    $book->setDate((int) $this->fixValue($fields[$i + $k + 1]));
                    break;

                case 'title':
                    $book->setTitle(trim($val, ':. '));
                    if ($fields[$i + 2] === '$b') {
                        $book->setDescription($this->fixValue($fields[$i + 3]));
                    }
                    break;

                case 'categ':
                    $book->setCategory(str_replace('--', ', ', $val));
                    break;

                case 'isbn':
                    $i = strpos($val, '(');
                    if ($i) {
                        $val = substr($val, 0, $i);
                    }
                    $isbn = (new \Access\Request\Params)->getIsbn($val, false);
                    if ($isbn) {
                        $book->setIsbn($isbn);
                    }
                    break;

                case 'pages':
                    $a = array_filter(explode(', ', $val), function($val) {
                        return (int) trim(str_replace('&nbsp;', ' ', $val)) > 0 || strpos($val, 'с.');
                    });
                    end($a);
                    $book->setPageCount((int) current($a));
                    break;
            }
            $key = '';
        }

        return $book;
    }

    protected function fixValue($value)
    {
        return trim(str_replace('&nbsp;', ' ', $value));
    }
}
