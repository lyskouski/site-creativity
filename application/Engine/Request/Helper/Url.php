<?php namespace Engine\Request\Helper;

use Engine\Validate\Helper;

/**
 * Parse url params from request
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine/Request/Helper
 */
class Url
{

    /**
     * @var string
     */
    protected $sLang;

    /**
     * @var string
     */
    protected $sType;

    /**
     * @var string
     */
    protected $sName;

    /**
     * @var array
     */
    protected $aParams;

    /**
     * Init URL helper
     * @fixme enable php_sapi_name under production
     */
    public function __construct()
    {
        $oInput = new \Engine\Request\Input();
        // Executed by cron
        if (\System\Registry::cron()) {
            $sExtra = implode('/', \System\Registry::cron());
            $this->bCron = true;
        // Fix Google and Yandex behaviour
        } else {
            $sExtra = $oInput->getServer('REQUEST_URI', '', FILTER_DEFAULT);
            (new \Deprecated\Migration)->checkSearch();
        }

        $aExtraParams = $this->parseExtraData($sExtra) + $oInput->getGet();
        $this->aParams = $aExtraParams;
        $this->checkLanguage();
    }

    /**
     * Parse request and prepare values
     *
     * @param string $extra
     * @return array
     */
    protected function parseExtraData($extra)
    {
        $listParams = array();
        if ($extra !== '/') {
            foreach (explode('/', strtok(trim($extra, '/'), '?')) as $i => $mValue) {
                $listParams[$i] = $mValue;
            }
        }

        if (strpos($extra, '?') !== false) {
            $sQuePos = strpos($extra, '?');
            $a = explode('&', substr($extra, $sQuePos + 1));
            $extra = substr($extra, 0, $sQuePos);
            foreach ($a as $sTmp) {
                $aTmp = explode('=', $sTmp);
                $listParams[$aTmp[0]] = isset($aTmp[1]) ? $aTmp[1] : $aTmp[0];
            }
        }
        return $listParams;
    }

    /**
     * Init language variable
     * @note trigger checkResponseType
     */
    protected function checkLanguage()
    {
        $sExtra = current($this->aParams);
        if (strlen($sExtra) === 2) {
            $this->sLang = $sExtra;
            unset($this->aParams[0]);
            reset($this->aParams);
        } else {
            $urlList = \System\Registry::config()->getUrlList();
            unset($urlList['default']);
            $host = (new \Engine\Request\Input)->getServer('HTTP_HOST');
            foreach ($urlList as $lang => $url) {
                if (strpos($url, $host)) {
                    $this->sLang = $lang;
                    break;
                }
            }
        }
        $this->checkResponseType();
    }

    /**
     * Get current language
     * @sample return 'ru'
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->sLang;
    }

    /**
     * Init response type variable
     * @note trigger checkModuleName
     */
    protected function checkResponseType()
    {
        $aParams = array();
        foreach ($this->aParams as $i => $m) {
            if (is_int($i)) {
                $aParams[$i] = $m;
            }
        }
        $ext = pathinfo(end($aParams), PATHINFO_EXTENSION);
        if (strlen($ext) <= 4) {
            $this->sType = $ext;
            $aParams[key($aParams)] = $this->aParams[key($aParams)] = str_replace(".{$this->sType}", '', end($aParams));
        }
        $this->checkModuleName($aParams);
    }

    /**
     * Get response type
     * @sample return 'html'
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->sType;
    }

    protected function checkModuleName($aExtra)
    {
        $config = \System\Registry::config();
        // Get target folder for modules
        $sDirPath = $config->getAppPath();

        reset($aExtra);
        if (current($aExtra) === '') {
            $aExtra[key($aExtra)] = 'index';
        }
        $sName = current($aExtra);
        $sUpperName = ucfirst($sName);
        $sBaseName = $config->getModulePrefix();
        while ($sName && preg_match('/^\w{1,}$/', $sName) && $sName === strtolower($sName) && is_dir($sDirPath . $sUpperName)) {
            $sDirPath .= $sUpperName . '/';
            $sBaseName .= '\\' . $sUpperName;
            $this->sName = $sBaseName;
            unset($this->aParams[key($aExtra)]);
            unset($aExtra[key($aExtra)]);
            $sName = current($aExtra);
            $sUpperName = ucfirst($sName);
        }
        // Define a default controller if missing
        if (is_null($this->sName)) {
            $this->sName = $config->getDefController();
        }

        // Rebuild params
        foreach ($aExtra as $i => $mValue) {
            unset($this->aParams[$i]);
        }
        $j = 0;
        foreach ($aExtra as $i => $mValue) {
            $this->aParams[$j] = $mValue;
            $j++;
        }
        ksort($this->aParams);
    }

    public function getModuleName()
    {
        return $this->sName;
    }

    public function getParams($bStrict = true)
    {
        $redirect = false;
        $aParams = array();
        $oFilter = new Helper\Filter();
        foreach ($this->aParams as $mKey => $mValue) {
            $aParams[$mKey] = html_entity_decode(urldecode($mValue));
            // Double encoding
            if (strpos($mValue, '%25') !== false && $aParams[$mKey] && $aParams[$mKey][0] = '%') {
                $redirect = true;
            }
            // JSON was sent via GET
            if ($mKey === 'account') {
                $oFilter->addSingle($mKey, FILTER_DEFAULT);
            // default filtering option
            } else {
                $oFilter->addSingle($mKey, FILTER_SANITIZE_STRING);
            }
        }

        if ($redirect) {
            $url = \System\Registry::config()->getUrl($this->sLang)
                . strtolower(str_replace(['\\', '/Modules/'], '/', $this->sName))
                . '/' . implode('/', $aParams)// html_entity_decode(urldecode())
                . '.' . $this->sType;
            (new \Deprecated\Migration)->redirect($url);
        }

        $oSanitize = new Helper\Sanitize($bStrict, $oFilter);
        return $oSanitize->get($aParams);
    }

}
