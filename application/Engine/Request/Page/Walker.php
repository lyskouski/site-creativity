<?php namespace Engine\Request\Page;

/**
 * CURL Walker
 *
 * Emulate user/browser behaviour for taking content from other sites
 *
 * @author Viachaslau Lyskouski
 * @since 2014-11-12
 */
class Walker
{

    /**
     * Store headers from requests
     * @var array
     */
    protected $aHeaders = array();
    protected $aReturnHeaders = array();
    protected $bDebugger;

    /**
     * Response
     * @var string
     */
    protected $sCode;

    /**
     * Previous url
     * @var string
     */
    protected $sUrl;

    /**
     * Cookies
     * @var string
     */
    protected $aCookie = array();

    /**
     * Init Walker (in debugging mode or without)
     * @param boolean $bDebugger
     */
    public function __construct($bDebugger = false)
    {
        $this->bDebugger = $bDebugger;
    }

    /**
     * Take form from response content
     *
     * @param string $sUrl
     * @param string $sId
     * @return array
     */
    public function getFormFromUrl($sUrl, $sId = null, $withAction = false)
    {
        $sContent = $this->checkUrl($sUrl)->request($sUrl, 'GET', array(), $this->getHeaders());
        return $this->getFormFromContent($sContent, $sId, $withAction);
    }

    /**
     * Get Document entity by content
     *
     * @param string $content
     * @return \DOMDocument
     */
    public function getDomDocument($content, $removeId = false)
    {
        if ($removeId) {
            $content = str_replace(' id="', ' class="', $content);
        }
        try {
            $doc = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $html = (new \System\Converter\Helper\Html)->repair($content);
            $doc->loadHTML("<!DOCTYPE html><html><head><meta content=\"text/html; charset=utf-8\" http-equiv=\"content-type\" /></head><body>{$html}</body></html>");
        } catch (\Exception $e) {
            if (!$removeId) {
                $doc = $this->getDomDocument($content, true);
            }
        }
        return $doc;
    }

    /**
     * Parse forms
     *
     * @param string $sContent
     * @param string $sId
     * @return array
     */
    public function getFormFromContent($sContent, $sId = null, $withAction = false)
    {
        $dom = $this->getDomDocument($sContent);
        /* @var $formList \DOMNodeList */
        $formList = $dom->getElementsByTagName('form');

        $formArray = array();
        /* @var $form \DOMDocument */
        foreach ($formList as $form) {
            $sKey = $form->getAttribute('id');
            while (isset($formArray[$sKey])) {
                $sKey .= '-';
            }
            $formArray[$sKey] = array();

            if ($withAction) {
                $formArray[$sKey]['action'] = $form->getAttribute('action');
            }
            $formDoc = new \DOMDocument('1.0', \Defines\Database\Params::ENCODING);
            $formDoc->appendChild($formDoc->importNode($form, true));
            $formDom = new \DomXPath($formDoc);
            // Find form elements
            $elements = $formDom->query("(//input|//select)");
            for ($i = 0; $i < $elements->length; $i++) {
                $node = $elements->item($i)->attributes;
                if (!$node || !$node->getNamedItem('name')) {
                    continue;
                }
                $value = '';
                if ($node->getNamedItem('value')) {
                    $value = $node->getNamedItem('value')->nodeValue;
                }
                $formArray[$sKey][$node->getNamedItem('name')->nodeValue] = $value;
            }

            // $inputs = $form->getElementsByTagName("input");
            // foreach ($inputs as $input) {
            //     $aForms[$sKey][$input->getAttribute("name")] = $input->getAttribute("value");
            // }
        }

        $aResult = $formArray;
        if ($sId) {
            $aResult = isset($formArray[$sId]) ? $formArray[$sId] : null;
        }

        return $aResult;
    }

    /**
     * Get content
     *
     * @param string $sUrl
     * @param string $sType
     * @param array $aParams
     * @return string
     */
    public function getContent($sUrl = null, $sType = 'GET', $aParams = array())
    {
        if (!$sUrl) {
            $sUrl = $this->sUrl;
        }
        return $this->checkUrl($sUrl)->request($sUrl, $sType, $aParams, $this->getHeaders());
    }

    /**
     * Submit data and take response
     *
     * @param array $aParams
     * @param string $sUrl
     * @return Http_Walker
     */
    public function postData(array $aParams, $sUrl = null)
    {
        if (!$sUrl) {
            $sUrl = $this->sUrl;
        }
        return $this->checkUrl($sUrl)->request($sUrl, 'POST', $aParams, $this->getHeaders());
    }

    /**
     * Convert xml-response into array
     *
     * @param string $sUrl
     * @return array
     */
    public function getXmlData($sUrl)
    {
        $sContent = $this->checkUrl($sUrl)->request($sUrl, 'GET', array(), $this->getHeaders());
        $oXml = new \SimpleXMLElement($sContent);
        $aOutput = (array) $oXml->Item;
        unset($aOutput['@attributes']);
        return $aOutput;
    }

    /**
     * Convert xml-response into array
     *
     * @param string $sContent
     * @return array
     */
    public function getXmlFromContent($sContent)
    {
        return new \SimpleXMLElement($sContent);
    }

    /**
     * Validate Url
     *
     * @param string $sUrl
     * @return Http_Walker
     * @throws Exception
     */
    protected function checkUrl($sUrl)
    {
        if (!filter_var($sUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception("Incorrect url: $sUrl");
        }
        return $this;
    }

    /**
     * Curl action
     *
     * @param string $sUrl
     * @param string $sMethod
     * @param array $aPostfields
     * @param array $aHeaders
     * @return string
     */
    protected function request($sUrl, $sMethod, array $aPostfields = array(), array $aHeaders = array())
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0)';
        // Set session cookie
        if ($this->aCookie) {
            $aCookie = array();
            foreach ($this->aCookie as $sKey => $sValue) {
                if (is_null($sValue)) {
                    $aCookie[] = $sKey;
                } else {
                    $aCookie[] = "$sKey=$sValue";
                }
            }
            $aHeaders[] = 'Cookie: ' . implode('; ', $aCookie);
            $aHeaders[] = 'User-Agent: ' . $userAgent;
        }
        if ($this->bDebugger) {
            var_dump("{COOKIE}", $this->aCookie);
            var_dump("{INIT: $sUrl}", $aHeaders);
        }
        $this->aHeaders = array();
        // Curl settings
        $aOptions = array(
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_BUFFERSIZE => 32000000,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADERFUNCTION => array($this, 'setHeader'),
            CURLOPT_HEADER => false
        );

        $proxy = \System\Registry::config()->getProxy();
        if ($proxy) {
            $aOptions[CURLOPT_PROXY] = $proxy;
        }

        if ($aHeaders) {
            $aOptions[CURLOPT_HTTPHEADER] = $aHeaders;
        }

        if ($this->sUrl) {
            $aOptions[CURLOPT_REFERER] = $this->sUrl;
        }
        $this->sUrl = $sUrl;
        // Form the request
        if (isset($aPostfields[0]) && sizeof($aPostfields) === 1) {
            $sQuery = $aPostfields[0];
        } else {
            $sQuery = http_build_query($aPostfields);
        }
        // Curl methods
        switch ($sMethod) {
            case 'POST':
                $aOptions[CURLOPT_URL] = $sUrl;
                $aOptions[CURLOPT_POST] = true;
                $aOptions[CURLOPT_POSTFIELDS] = $sQuery;
                break;

            default:
                if ($sQuery) {
                    $sUrl .= '?' . $sQuery;
                }
                $aOptions[CURLOPT_URL] = $sUrl;
                $aOptions[CURLOPT_CUSTOMREQUEST] = $sMethod;
        }

        $oCurl = curl_init($sUrl);
        curl_setopt_array($oCurl, $aOptions);
        $sResponse = curl_exec($oCurl);
        $this->sCode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        $this->aHeaders = array_merge($this->aHeaders, curl_getinfo($oCurl));
        if ($this->bDebugger) {
            var_dump("{ENDED $sUrl}", $this->aHeaders);
        }
        curl_close($oCurl);
        return $sResponse;
    }

    public function getHttpCode()
    {
        return $this->sCode;
    }

    public function getHeader($sName = null)
    {
        $value = null;
        if (is_null($sName)) {
            $value = $this->aHeaders;
        } else {
            $sKey = trim(str_replace('-', ' ', strtolower($sName)));
            if (isset($this->aHeaders[$sKey])) {
                $value = $this->aHeaders[$sKey];
            }
        }
        return $value;
    }

    /**
     * Initial headers
     *
     * @return array
     */
    protected function getHeaders()
    {
        $aHeader = array();
        if (isset($this->aHeaders['set cookie'])) {
            $a = explode(';', $this->aHeaders['set cookie']);
            foreach ($a as $s) {
                if (strpos($s, '=')) {
                    $ai = explode('=', $s);
                    $sKey = $ai[0];
                    unset($ai[0]);
                    $this->aCookie[trim($sKey)] = trim(implode('=', array_values($ai)));
                } elseif (trim($s)) {
                    $this->aCookie[trim($s)] = null;
                }
            }
        }
        if (isset($this->aHeaders['custom content type'])) {
            $aHeader[] = "Content-Type: {$this->aHeaders['custom content type']}";
        }

        if (isset($this->aHeaders['location'])) {
            $aUrl = explode('/', $this->sUrl);
            $this->sUrl = "{$aUrl[0]}//{$aUrl[1]}{$this->aHeaders['location']}";
        }
        foreach ($this->aReturnHeaders as $sKey => $sValue) {
            $aHeader[] = str_replace(' ', '-', ucwords($sKey)) . ': ' . $sValue;
        }

        return $aHeader;
    }

    /**
     * Set the header info to store
     *
     * @return string
     */
    public function setHeader($oCurl, $sHeader, $bInclude = false, $bOverride = false)
    {
        if ($this->bDebugger) {
            echo "<br />Set Header: <code>$sHeader</code>";
        }

        $i = strpos($sHeader, ':', 1);
        if (!empty($i)) {
            $sKey = trim(str_replace('-', ' ', strtolower(substr($sHeader, 0, $i))));
            $sValue = trim(substr($sHeader, $i + 2));
            $aHeader = &$this->{$bInclude ? 'aReturnHeaders' : 'aHeaders'};
            if (!isset($aHeader[$sKey]) || $bOverride) {
                $aHeader[$sKey] = $sValue;
            } else {
                $aHeader[$sKey] .= ";$sValue";
            }
        }
        return strlen($sHeader);
    }
}
