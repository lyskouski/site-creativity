<?php namespace System\Converter;

class Massive
{

    /**
     * Prepare list of titles
     * @return type
     */
    public function getCategories($aData)
    {
        $fAdd = function($aResult, $aList, $fAdd) {
            foreach ($aList as $key => $m) {
                if ($key === 'title') {
                    $aResult[] = $m;
                } elseif (is_array($m)) {
                    $aResult = $fAdd($aResult, $m, $fAdd);
                }
            }
            return $aResult;
        };
        return $fAdd(array(), $aData, $fAdd);
    }

    /**
     * Convert searchable attributes
     *
     * @param string $sUrl
     * @return array
     */
    public function getConvertable($sUrl)
    {
        preg_match("#/search/(.*?)+[/.]#", $sUrl, $match);
        $aConvert = array();
        if ($match) {
            $key = str_replace('&#39;', "'", substr($match[0], strlen('/search/'), -1));
            $list = $this->getConvList();
            if (array_key_exists($key, $list)) {
                $aConvert[str_replace("'", '&#39;', $key)] = $list[$key];
            }
        }
        return $aConvert;
    }

    /**
     * Get list of a category keys
     *
     * @return array
     */
    protected function getConvList()
    {
        $a = $this->getCategories(array_merge(
            \Defines\Catalog::getMind(false),
            \Defines\Catalog::getOeuvre(false)
        ));
        $oTranslate = \System\Registry::translation();

        $list = array();
        foreach ($a as $key) {
            $list[$oTranslate->sys("{$key}")] = $key;
        }
        return $list;
    }

}
