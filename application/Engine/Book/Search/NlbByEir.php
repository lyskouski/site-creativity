<?php namespace Engine\Book\Search;

use Engine\Request\Page\Walker;

/**
 * Search by using eir.nlb.by
 * @todo cover results
 *
 * @since 2016-09-22
 * @author Viachaslau Lyskouski
 */
class NlbByEir extends WalkerAbstract
{
    const SEARCH_AUTHOR = 1003;
    const SEARCH_ISBN = 7;
    const SEARCH_TITLE = 4;
    const SEARCH_NONE = 1016;

    public function __construct($language = null)
    {
        parent::__construct($language);
        $this->url = 'http://eir.nlb.by';
    }

    protected function getElements(\DomXPath $data, $search = '', $list = array())
    {
        $elements = $data->query($search);
        for ($i = 0; $i < $elements->length; $i++) {
            $node = $elements->item($i)->attributes;
            $value = '';
            if ($node->getNamedItem('value')) {
                $value = $node->getNamedItem('value')->nodeValue;
            }
            if ($node->getNamedItem('name')) {
                $list[$node->getNamedItem('name')->nodeValue] = $value;
            }
        }
        return $list;
    }

    protected function getSearchForm()
    {
        $this->walker->getContent($this->url . '/wps/portal/eir/');
        if (!$this->walker->getHeader('redirect_url')) {
            throw new \Error\Validation('[NLBe] Broken Search URL', \Defines\Response\Code::E_FATAL);
        }
        $mainPage = $this->walker->getContent($this->walker->getHeader('redirect_url'));
        // Goto search form by finding URL
        if (!$mainPage) {
            throw new \Error\Validation('[NLBe] Broken Search Init', \Defines\Response\Code::E_FATAL);
        }
        $dom = $this->walker->getDomDocument($mainPage, true);
        $searchUrl = (new \DomXPath($dom))->query("//a[contains(@class, 'extendedsearch')]");
        if (!$searchUrl->length) {
            throw new \Error\Validation('[NLBe] Broken Search Bind', \Defines\Response\Code::E_FATAL);
        }
        // Get Search Page
        //return $walker->getFormFromUrl($this->url . $searchUrl->item(0)->attributes->getNamedItem('href')->nodeValue);
        $page = $this->walker->getContent($this->url . $searchUrl->item(0)->attributes->getNamedItem('href')->nodeValue);
        $pageDom = new \DomXPath($this->walker->getDomDocument($page, true));
        // submit path
        $action = $pageDom->query("//form")->item(1)->attributes->getNamedItem('action')->nodeValue;
        // Find form elements
        return $this->getElements($pageDom, "(//input|//select)", ['action' => $action]);
    }

    protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = null, $val3 = '', $key3 = null)
    {
        if (is_null($key2)) {
            $key2 = static::SEARCH_NONE;
        }
        if (is_null($key3)) {
            $key3 = static::SEARCH_NONE;
        }

        $form = $this->getSearchForm($this->walker);
        $postUrl = $form['action'];
        unset($form['action']);
        unset($form['searchstring']);

        $keyId = null;
        foreach (array_keys($form) as $key) {
            if (strpos($key, ':form')) {
                $keyId = substr($key, 0, strpos($key, ':form'));
                break;
            }
        }
        unset($form["{$keyId}:form:dataTable:button"]);

        $allKeys = array_keys($form);
        foreach ($allKeys as $i => $key) {
            if ($key === "{$keyId}:form:popUpSearch") {
                $form[$allKeys[$i+1]] = 'all';

            } elseif ($key === "{$keyId}:form:dataTable:0:term") {
                $form[$key] = $val1;
                $form[$allKeys[$i+1]] = $key1;
                $form[$allKeys[$i+2]] = 'none';
                $form[$allKeys[$i+3]] = 'И';

            } elseif ($key === "{$keyId}:form:dataTable:1:term") {
                $form[$key] = $val2;
                $form[$allKeys[$i+1]] = $key2;
                $form[$allKeys[$i+2]] = 'none';
                $form[$allKeys[$i+3]] = 'И';

            } elseif ($key === "{$keyId}:form:dataTable:2:term") {
                $form[$key] = $val3;
                $form[$allKeys[$i+1]] = $key3;
                $form[$allKeys[$i+2]] = 'none';
                $form[$allKeys[$i+3]] = 'true';
                $j = 4;
                $keyClear = substr($allKeys[$i+3], 0, strpos($allKeys[$i+3], ':0:'));
                while (strpos($allKeys[$i + $j], $keyClear) !== false) {
                    unset($form[$allKeys[$i+$j]]);
                    $j++;
                }
            }
        }

        $this->walker->postData($form, $this->url . $postUrl);
        if (!$this->walker->getHeader('location')) {
            throw new \Error\Validation('[NLBe] Broken Search Results', \Defines\Response\Code::E_FATAL);
        }
        $resultUrl = $this->walker->getHeader('location');
        return $this->walker->getContent($resultUrl);
    }

    protected function prepareResult($content)
    {
        // ...
    }
}
