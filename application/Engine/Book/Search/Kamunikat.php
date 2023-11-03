<?php namespace Engine\Book\Search;

/**
 * Description of Kamunikat
 *
 * @author s.lyskovski
 */
class Kamunikat extends WalkerAbstract
{
    public function __construct($language = null)
    {
        parent::__construct($language);
        $this->url = 'http://kamunikat.org';
    }

    protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = null, $val3 = '', $key3 = null)
    {
        $list = array();

        for ($i = 1; $i < 4; $i++) {
            if (!(${'val'.$i} && ${'key'.$i})) {
                continue;
            }

            switch (${'key'.$i}) {
                case self::SEARCH_ISBN:   $key = 'isbn';  break;
                case self::SEARCH_AUTHOR: $key = 'autor'; break;
                case self::SEARCH_TITLE:  $key = 'tytul'; break;
                default: $key = 'wszystkie';
            }

            $form = array(
                'SearchWhat'=> ${'val'.$i},
                'SearchBy'=> $key,
                'SearchWhere'=>'knihi',
                'SearchCatalogue'=>'ALL',
                'SortBy'=>'utw_kiedy',
                'SortDir'=>'ASC',
                'Search.x'=> 40 + mt_rand(0, 5),
                'Search.y'=> 15 + mt_rand(0, 5)
            );

            $content = $this->walker->getContent($this->url . '/poszuk_kataloh.html?' . http_build_query($form));
            $list[] = $this->bindResult($this->walker->getDomDocument($content));
        }
        return $list;
    }

    protected function prepareResult($content)
    {
        if (sizeof($content) > 1) {
            $result = call_user_func_array('array_intersect_key', $content);
        } else {
            reset($content);
            $result = current($content);
        }
        return $result;
    }

    protected function bindResult($content)
    {
        $result = array();
        $find = new \DomXPath($content);
        foreach ($find->query("//*[contains(@class, 'DocContentContainer')]") as $node) {
            $new = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $new->appendChild($new->importNode($node, true));

            $search = new \DomXPath($new);
            foreach ($search->query("//a") as $link) {
                $url = $link->attributes->getNamedItem('href')->nodeValue;
                $result[$url] = $this->parseBookPage($url);
            }
        }
        return $result;
    }

    protected function parseBookPage($url)
    {
        $book = new \Engine\Book\Result\Book();

        $html = $this->walker->getContent($this->url . '/' . $url);
        $content = $this->walker->getDomDocument($html);
        $find = new \DomXPath($content);

        $book->setText($this->getNode($find->query("//*[contains(@class, 'VolumeSummary')]")))
            ->setTitle($this->getNode($find->query("//h1")))
            ->setAuthor($this->getNode($find->query("//h3"), 1))
            ->setCategory($this->getNode($find->query("//h3")));

        $imgKey = 'meta property="og:image" content="';
        $i = strpos($html, $imgKey);
        if ($i) {
            $img = substr($html, $i +  strlen($imgKey));
            $book->setImage(substr($img, 0, strpos($img, '"')));
        }

        $other = explode("\n", $this->getNode($find->query("//*[contains(@class, 'VolumeNote')]")));
        foreach ($other as $tmp) {
            $a = explode(': ', $tmp);
            switch (trim($a[0])) {
                case 'Дата выхаду':
                    $book->setDate((int) $a[1]);
                    break;
                case 'Памеры':
                    $book->setPageCount((int) $a[1]);
                    break;
                case 'Кнігазбор':
                    $book->setDescription($a[1] . '| БIБ Kamunikat');
                    break;
                case 'ISBN':
                    $isbn = (new \Access\Request\Params)->getIsbn($a[1], false);
                    $book->setIsbn($isbn);
                    break;
                case 'УДК':
                    $book->setUdc($a[1]);
                    break;
                case 'Катэгорыя':
                    $cat = array_map('trim', explode(',', $book->getCategory()));
                    $cat[] = $a[1];
                    $book->setCategory(implode(', ', $cat));
                    break;
            }
        }

        return $book;
    }


}
