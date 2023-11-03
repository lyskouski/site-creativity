<?php namespace Engine\Book\Search;

/**
 * Search by using nlb.by
 * @todo cover results
 *
 * @since 2016-09-22
 * @author Viachaslau Lyskouski
 */
class NlbBy extends WalkerAbstract
{
    const SEARCH_AUTHOR = 1003;
    const SEARCH_ISBN = 17;
    const SEARCH_TITLE = 4;
    const SEARCH_NONE = '';

    public function __construct($language = null)
    {
        parent::__construct($language);
        $this->url = 'http://www.nlb.by';

        switch ($language) {
            case \Defines\Language::BE:
                $this->language = 'bel';
                break;

            default:
                $this->language = 'ru';
        }
    }

    protected function getSearchForm()
    {
        $initUrl = $this->url . '/portal/page/portal/index/resources/expandedsearch';
        $tmp = $this->walker->getContent($initUrl);

        $url = substr($tmp, strpos($tmp, 'classId'));
        $searchUrl = $this->url
            . '/portal/page/portal/index/resources/anothersearch'
            . '?searchType=book&submitR=reset&lang='
            . $this->language . '&' . substr($url, 0, strpos($url, '"'));

        $content = $this->walker->getContent($searchUrl);
        //$dom = $this->walker->getDomDocument($content);
        ///* @var $form \DOMNode */
        //foreach ($dom->getElementsByTagName('iframe') as $node) {
        //    $this->walker->getContent($this->url .$node->getAttribute('src'));
        //}
        //foreach ($dom->getElementsByTagName('img') as $node) {
        //    $this->walker->getContent($this->url .$node->getAttribute('src'));
        //}

        $formList = $this->walker->getFormFromContent($content, null, true);

        if (!isset($formList['-'])) {
            throw new \Error\Validation('[NLB] Form is missing', \Defines\Response\Code::E_FATAL);
        }

        $form = $formList['-'];
        unset($form['reset']);
        unset($form['statistic']);
        return $form;
    }

    protected function bindSearchForm($val1, $key1, $val2 = '', $key2 = self::SEARCH_NONE, $val3 = '', $key3 = self::SEARCH_NONE)
    {
        $form = $this->getSearchForm();
        $postUrl = $form['action'];
        unset($form['action']);

        $keyId = null;
        foreach (array_keys($form) as $key) {
            if (strpos($key, '.')) {
                $keyId = substr($key, 0, strpos($key, '.'));
                break;
            }
        }

        for ($i = 1; $i < 4; $i++) {
            if (!(${'val'.$i} && ${'key'.$i})) {
                continue;
            }

            switch (${'key'.$i}) {
                case self::SEARCH_ISBN:
                    $form["{$keyId}.allelem"] = ${'val'.$i};
                    break;
                case self::SEARCH_AUTHOR:
                    $form["{$keyId}.authorbibl"] = ${'val'.$i};
                    $form["{$keyId}.authorbibl_searchtype"] = 'any';
                    break;
                case self::SEARCH_TITLE:
                    $form["{$keyId}.title"] = ${'val'.$i};
                    $form["{$keyId}.title_searchtype"] = 'any';
                    break;
                default: $key = 'wszystkie';
            }
        }
        // Language
        if ($this->language) {
            $form["{$keyId}.doclang"] = $this->language;
        }
        $form['searchType'] = 'book';
        $form['submitR'] = '';
        $form['searchresult'] = 0;

        $this->walker->setHeader(null, 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', true, true);
        $this->walker->setHeader(null, 'Upgrade-Insecure-Requests: 1', true, true);
        $content = $this->walker->postData($form, $postUrl);
        //if (strpos($content, 'Поиск может занять несколько минут.')) {
        //    sleep(5);
        //    $content = $walker->getContent();
        //}
        return $content;
    }

    protected function prepareResult($content)
    {
        // ...
    }
}
