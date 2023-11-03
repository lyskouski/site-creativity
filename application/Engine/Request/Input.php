<?php namespace Engine\Request;

/**
 * Query params - GET/POST/COOKIE/SERVER/SESSION pre-processing
 * @link http://php.net/manual/en/function.filter-input.php
 * @link http://php.net/manual/en/function.filter-input-array.php
 * @link http://php.net/manual/en/function.filter-var.php
 * @link http://php.net/manual/en/function.filter-var-array.php
 *
 * Skip programatically modifying the superglobal request variable and define and use new ordinary variables
 * The whole point of filter_input function is to eventually REMOVE the usage of superglobal variables,
 * since they are the single most dangerous point of all PHP scripts and most frequent cause of security vulnerabilities (XSS and SQL injections)
 *
 * @sample (new \Engine\Request\Input)->getPost()
 *
 * @note DO NOT implement Singleton/Fabric Pattern here, it is not the right way.
 *       From an OOP perspective there is no reason they should ever be chosen.
 *       static $aOverride is used only because of operations with a global scope (POST / GET / ...)
 *       PERFORMANCE TESTS (microtime)
 *       number requests      [1] fabric (sec)    [2] singelton (sec)   [3] object call (sec)
 *       10 000 000           1.116432            0.491059              0.021288
 *       [1] \Engine\Registry::input()->getGet();
 *       [2] \Engine\Request\Input::getInstance()->getGet();
 *       [3] (new \Engine\Request\Input)->getGet();
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Engine/Request
 */
class Input
{

    const DISABLE_SCOPE = 'disable';

    /**
     * @var array
     */
    protected static $aOverride = array(
        INPUT_GET => array(),
        INPUT_POST => array(),
        INPUT_COOKIE => array(),
        INPUT_SERVER => array(),
        INPUT_SESSION => array(),
        self::DISABLE_SCOPE => array(
            INPUT_GET => false,
            INPUT_POST => false,
            INPUT_COOKIE => false,
            INPUT_SERVER => false,
            INPUT_SESSION => false,
        )
    );

    /**
     * Get validation constants for additional check
     * @note value can be converted to `false`, it has to be set default in that case
     * @note FILTER_VALIDATE_BOOLEAN is not needed there
     * @note FILTER_VALIDATE_MAC skipped, coz of error: "use of undefined constant FILTER_VALIDATE_MAC" - teamcity incompatibility (php 5.4)
     *
     * @return array
     */
    protected function getValidationList()
    {
        return array(
            FILTER_VALIDATE_EMAIL,
            FILTER_VALIDATE_FLOAT,
            FILTER_VALIDATE_INT,
            FILTER_VALIDATE_IP,
            FILTER_VALIDATE_REGEXP,
            FILTER_VALIDATE_URL
        );
    }

    /**
     * Clear overrride list
     *
     * @param integer $iType - type of request: INPUT_(GET|POST|..)
     * @return \Engine\Request\Input
     */
    public function clearOverride($iType = INPUT_GET)
    {
        self::$aOverride[$iType] = array();
        self::$aOverride[self::DISABLE_SCOPE][$iType] = false;
        return $this;
    }

    /**
     * "Override" the global input array (POST/GET/...)
     *
     * @param array $aData - scope that will be used for specified INPUT_(GET|POST|...)
     * @param integer $iType - type of request: INPUT_(GET|POST|..)
     * @return \Engine\Request\Input
     */
    public function fake(array $aData, $iType = INPUT_GET)
    {
        self::$aOverride[$iType] = $aData;
        self::$aOverride[self::DISABLE_SCOPE][$iType] = true;
        return $this;
    }

    /**
     * Filter value
     *
     * @param mixed $mValue
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    protected function filterValue($mValue, $mFilterType)
    {
        if (\System\Aggregator::is_array($mValue)) {
            $mValue = filter_var_array($mValue, $mFilterType);
        } else {
            $mValue = filter_var($mValue, $mFilterType);
        }
        return $mValue;
    }

    /**
     * Internal functionality to take full list from a global scopes
     *
     * @param integer $iType - type of request: INPUT_(GET|POST|..)
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return array
     */
    protected function getArrayValue($iType, $mFilterType)
    {
        // return counterfeit data
        if (self::$aOverride[self::DISABLE_SCOPE][$iType]) {
            $mValue = $this->filterValue(self::$aOverride[$iType], $mFilterType);
        }
        // filter INPUT array
        else {
            $aTempValue = array_merge(
                (array) filter_input_array($iType, FILTER_DEFAULT),
                (array) self::$aOverride[$iType]
            );
            // Validate Form
            $mValue = (new \Engine\Validate\Form)->sanitize($aTempValue, $mFilterType);
        }
        return $mValue;
    }

    /**
     * Internal functionality to take values from a global scopes
     *
     * @param integer $iType - type of request: INPUT_(GET|POST|..)
     * @param string $sName
     * @param mixed $mDefault
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    protected function getValue($iType, $sName, $mDefault, $mFilterType)
    {
        // get fulll list
        if (is_null($sName)) {
            $mValue = $this->getArrayValue($iType, $mFilterType);
        }
        // return overriden field
        elseif (array_key_exists($sName, self::$aOverride[$iType])) {
            $mValue = $this->filterValue(self::$aOverride[$iType][$sName], $mFilterType);
        }
        // filter INPUT field
        else {
            $mValue = filter_input($iType, $sName, $mFilterType, FILTER_REQUIRE_ARRAY);
            if ($mValue === false) {
                $mValue = filter_input($iType, $sName, $mFilterType);
            }
        }

        if (
            // NULL if the variable_name variable is not set
            is_null($mValue)
            // Check if the validation was failed
            || $mValue === false && in_array($mFilterType, $this->getValidationList())
        ) {
            $mValue = $mDefault;
        }

        return $mValue;
    }

    /**
     * Override GET value
     *
     * @param string $sName
     * @param mixed $mValue
     * @param boolean $bIgnoreExist
     * @return \Engine\Request\Input
     */
    public function setGet($sName, $mValue, $bIgnoreExist = true)
    {
        if ($bIgnoreExist || !array_key_exists($sName, $this->getGet())) {
            self::$aOverride[INPUT_GET][$sName] = $mValue;
        }
        return $this;
    }

    /**
     * Get filtered GET value
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getGet($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        return $this->getValue(INPUT_GET, $sName, $mDefault, $mFilterType);
    }

    /**
     * Override POST value
     *
     * @param string $sName
     * @param mixed $mValue
     * @param boolean $bIgnoreExist
     * @return \Engine\Request\Input
     */
    public function setPost($sName, $mValue, $bIgnoreExist = true)
    {
        if ($bIgnoreExist || !array_key_exists($sName, $this->getPost())) {
            self::$aOverride[INPUT_POST][$sName] = $mValue;
        }
        return $this;
    }

    /**
     * Get filtered POST value
     * @note return false - if filter is failed
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getPost($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        return $this->getValue(INPUT_POST, $sName, $mDefault, $mFilterType);
    }

    /**
     * Combination of GET and POST
     * @note POST parameter will override GET value
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getParam($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        if (is_null($sName)) {
            $mValue = $this->getParams();
        } elseif (array_key_exists($sName, $this->getPost())) {
            $mValue = $this->getPost($sName, $mDefault, $mFilterType);
        } else {
            $mValue = $this->getGet($sName, $mDefault, $mFilterType);
        }
        return $mValue;
    }

    /**
     * Get register insensitive param
     * @see self::getParam
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getInsParam($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        $mValue = $mDefault;
        $sLowerName = strtolower($sName);
        $aValues = array_change_key_case($this->getParams(FILTER_DEFAULT), CASE_LOWER);
        if (array_key_exists($sLowerName, $aValues)) {
            $mValue = $aValues[$sLowerName];
        }
        return $this->filterValue($mValue, $mFilterType);
    }

    /**
     * Alias of getParam function
     *
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return array
     */
    public function getParams($mFilterType = FILTER_DEFAULT)
    {
        return array_merge(
            (array) $this->getGet(null, null, $mFilterType), (array) $this->getPost(null, null, $mFilterType)
        );
    }

    /**
     * Set SESSION value
     *
     * @param string $sName
     * @param mixed $mValue
     * @param integer $iExpireDays
     * @return \Engine\Request\Input
     */
    public function setCookie($sName, $mValue, $iExpireDays = 1)
    {
        self::$aOverride[INPUT_COOKIE][$sName] = $mValue;

        //@codeCoverageIgnoreStart
        if ($this->getServer('HTTP_USER_AGENT')) {
            $iTime = time() + 60 * 60 * 24 * $iExpireDays;
            $aParams = session_get_cookie_params();
            setcookie($sName, $mValue, $iTime, '/', $aParams['domain'], $aParams['secure']);
        }
        //@codeCoverageIgnoreEnd
        return $this;
    }

    /**
     * Delete COOKIE value
     *
     * @param string $sName
     * @return \Engine\Request\Input
     */
    public function delCookie($sName)
    {
        // @note it has to be so because filter_input( INPUT_COOKIE ) cannot be cleared
        self::$aOverride[INPUT_COOKIE][$sName] = null;
        //@codeCoverageIgnoreStart
        if ($this->getServer('HTTP_USER_AGENT')) {
            $aParams = session_get_cookie_params();
            // Clear browser cookies
            setcookie($sName, '', time(), '/', $aParams['domain'], $aParams['secure']);
        }
        //@codeCoverageIgnoreEnd
        return $this;
    }

    /**
     * Get filtered COOKIE value
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getCookie($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        return $this->getValue(INPUT_COOKIE, $sName, $mDefault, $mFilterType);
    }

    /**
     * Override SERVER value
     * @note use with extreme caution
     *
     * @param string $sName
     * @param mixed $mValue
     * @return \Engine\Request\Input
     */
    public function setServer($sName, $mValue)
    {
        self::$aOverride[INPUT_SERVER][$sName] = $mValue;
        return $this;
    }

    /**
     * Get filtered SERVER value
     * @note filter_input(INPUT_SERVER) it is "not intended" to be used from CLI
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getServer($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        if (!filter_has_var(INPUT_SERVER, "SERVER_NAME") && !isset(self::$aOverride[INPUT_SERVER]['SERVER_NAME'])) {
            $aChanged = self::$aOverride[INPUT_SERVER];
            $this->fake($_SERVER, INPUT_SERVER);
            foreach ($aChanged as $sName => $mValue) {
                $this->setServer($sName, $mValue);
            }
        }
        return $this->getValue(INPUT_SERVER, $sName, $mDefault, $mFilterType);
    }

    /**
     * Set SESSION value
     *
     * @param string $sName
     * @param mixed $mValue
     * @return \Engine\Request\Input
     */
    public function setSession($sName, $mValue)
    {
        // hack for phpunit tests
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }

        $_SESSION[$sName] = $mValue;
        self::$aOverride[INPUT_SESSION][$sName] = $mValue;
        return $this;
    }

    /**
     * Delete SESSION value
     *
     * @param string $sName
     * @return \Engine\Request\Input
     */
    public function delSession($sName)
    {
        // hack for phpunit tests
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }

        unset(self::$aOverride[INPUT_SESSION][$sName]);
        unset($_SESSION[$sName]);
        return $this;
    }

    /**
     * Get filtered SESSION value
     * @note PHP 5.6 {warning} filter_input_array(): INPUT_SESSION is not yet implemented
     *
     * @todo change function content to one line (after PHP 7+ migration):
     * return $this->getValue( INPUT_SESSION, $sName, $mDefault, $mFilterType );
     *
     * @param string $sName - `null` to return all values as array
     * @param mixed $mDefault - return if the value is missing
     * @param integer|array $mFilterType - FILTER_(VALIDATE|SANITIZE)_{TYPE}
     * @return mixed
     */
    public function getSession($sName = null, $mDefault = null, $mFilterType = FILTER_DEFAULT)
    {
        // hack for phpunit tests
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }

        $mValue = null;
        if (is_null($sName)) {
            $mValue = array_merge((array) filter_var_array($_SESSION, $mFilterType), self::$aOverride[INPUT_SESSION]);
        } elseif (array_key_exists($sName, self::$aOverride[INPUT_SESSION])) {
            $mValue = $this->filterValue(self::$aOverride[INPUT_SESSION][$sName], $mFilterType);
        } elseif (array_key_exists($sName, $_SESSION)) {
            $mValue = $this->filterValue($_SESSION[$sName], $mFilterType);
        }

        // NULL if the variable_name variable is not set
        if (is_null($mValue)) {
            $mValue = $mDefault;
        }

        return $mValue;
    }

    /**
     * Protocol identification
     *
     * @return string - `http` or `https`
     */
    public function getUrlProtocol()
    {
        $sType = 'http';
        $ServerType = $this->getServer('HTTPS', '', FILTER_SANITIZE_STRING);
        if ($ServerType && $ServerType !== 'off') {
            $sType = 'https';
        }
        return $sType;
    }

    /**
     * Get application URL
     *
     * @param (boolean|null|string) $mFilePath - should the filepath be added
     * @param array $aExtra - extra parameters for a GET-request
     * @return string
     */
    public function getUrl($mFilePath = false, array $aExtra = array(), $withoutPage = true)
    {
        // Site address
        $sUrl = \System\Registry::config()->getUrl();

        $sMask = '/' . \System\Registry::translation()->getTargetLanguage() . '/';
        $relativeUrl = (new Input\Server)->getRelativePath();
        // Remove language prefix
        if (strpos($relativeUrl, $sMask) === 0) {
            $relativeUrl = '/' . substr($relativeUrl, strlen($sMask));
        }

        // Basic path to script
        if (is_string($mFilePath)) {
            $sUrl .= '/' . $mFilePath;

        } elseif ($mFilePath) {
            $sUrl .= $relativeUrl;

        } elseif (is_null($mFilePath)) {
            $sUrl = $relativeUrl;
            // Remove extra-values
            if (strpos($sUrl, '?')) {
                $sUrl = substr($sUrl, 0, strpos($sUrl, '?'));
            }
            // Remove extension suffix
            $a = explode('.', $sUrl);
            $s = end($a);
            if (in_array($s, \Defines\Extension::getList())) {
                $sUrl = substr($sUrl, 0, - (1 + strlen($s)));
            }
            // Remove pagination value
            $a2 = explode('/', $sUrl);
            $i = end($a2);
            if ($withoutPage && (string) (int) $i === (string) $i) {
                $sUrl = substr($sUrl, 0, - (1 + strlen($i)));
            }

            // Check if it's a home page
            if (!trim($sUrl, '/')) {
                $sUrl = 'index';
            }

            $sUrl = str_replace('/comment', '', trim($sUrl, '/'));
        }
        // Extra params
        if ($aExtra) {
            $sUrl .= '?' . http_build_query($aExtra, '', '&amp;');
        }
        return $sUrl;
    }

    public function getDirectUrl($sPath, $sExt = null)
    {
        $sLang = \System\Registry::translation()->getTargetLanguage();
        if (is_null($sExt)) {
            $sExt = \Defines\Extension::getDefault();
        }
        return $this->getUrl($sLang . $sPath . '.' . $sExt);
    }

    /**
     * Get referer url (to redirect back)
     *
     * @return string
     */
    public function getRefererUrl()
    {
        $sRedirect = $this->getServer('HTTP_REFERER', '', FILTER_SANITIZE_STRING);
        if (!strpos($sRedirect, '.html')) {
            $sLang = \System\Registry::translation()->getTargetLanguage();
            $sExt = \Defines\Extension::getDefault();
            $sRedirect = $this->getUrl('index.' . $sExt);
        }
        return $sRedirect;
    }
}
