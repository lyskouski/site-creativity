<?php namespace Access;

use System\ArrayUndef;
use Defines\Response\Code;

/**
 * Check allowed parameter of request
 *
 * @since 2016-01-31
 * @author Viachaslau Lyskouski
 */
class Allowed
{

    protected $aData;
    protected $aReqData = array();

    protected $iType;

    protected $sExt;
    protected $error;

    public function __construct()
    {
        $this->aData = array();
    }

    /**
     * Add request method by type (POST, GET, etc)
     *
     * @param integer $iType - const from \Defines\RequestMethod
     * @return \Access\Allowed
     * @throws \Error\Application
     */
    public function addRequestMethod($iType)
    {
        if (!in_array($iType, \Defines\RequestMethod::getList())) {
            throw new \Error\Application('Incorrect implementation: RequestMethod');
        }
        $this->iType = $iType;
        if (!isset($this->aData[$iType])) {
            $this->aData[$iType] = array();
        }
        return $this;
    }

    public function delRequestMethod($iType)
    {
        unset ($this->aData[$iType]);
        return $this;
    }

    /**
     * Bind extension to a Request Method
     * @param type $sExt
     * @return \Access\Allowed\Request
     * @throws \Error\Application
     */
    public function bindExtension($sExt)
    {
        if (is_null($this->iType)) {
            throw new \Error\Application('Missing RequestMethod');
        }
        if (!in_array($sExt, \Defines\Extension::getList())) {
            throw new \Error\Application('Incorrect implementation: Extension');
        }
        $this->sExt = $sExt;
        if (!isset($this->aData[$this->iType][$sExt])) {
            $this->aData[$this->iType][$sExt] = null;
        }
        return $this;
    }

    public function unbindExtension($sExt)
    {
        if (is_null($this->iType) || !isset($this->aData[$this->iType])) {
            throw new \Error\Application('Missing RequestMethod');
        }
        unset ($this->aData[$this->iType][$sExt]);
        return $this;
    }

    /**
     * Bind extension to a Request Method
     * @param type $sExt
     * @return \Access\Allowed\Request
     * @throws \Error\Application
     */
    public function copyToExtension($sExt)
    {
        if (is_null($this->iType)) {
            throw new \Error\Application('Missing RequestMethod');
        }
        if (is_null($this->sExt) || ! array_key_exists($this->sExt, $this->aData[$this->iType])) {
            throw new \Error\Application('Missing Extension');
        }
        if (!in_array($sExt, \Defines\Extension::getList())) {
            throw new \Error\Application('Incorrect implementation: Extension');
        }
        $this->aData[$this->iType][$sExt] = $this->aData[$this->iType][$this->sExt];
        $this->sExt = $sExt;
        return $this;
    }

    /**
     * Add allowed option for a parameter
     *
     * @param string $sKey
     * @param array $aParams
     * @param boolean $bRequired
     */
    public function bindKey($sKey, array $aParams = null, $bRequired = false)
    {
        if (is_null($this->iType) || is_null($this->sExt)) {
            throw new \Error\Application('Incorrect implementation: bindKey');
        }
        $this->aData[$this->iType][$this->sExt][$sKey] = $aParams;
        if ($bRequired) {
            $this->aReqData[] = $sKey;
        }
        return $this;
    }

    /**
     * Prevent any parameters for the current type of request
     *
     * @return \Access\Allowed
     */
    public function bindNullKey()
    {
        if (is_null($this->iType) || is_null($this->sExt)) {
            throw new \Error\Application('Incorrect implementation: bindNullKey');
        }
        $this->aData[$this->iType][$this->sExt] = array();
        return $this;
    }

    /**
     * Predefined comment validation
     * @deprecated
     *
     * @param \Engine\Request\Input $oInput
     */
    protected function checkExtraRequests($oInput)
    {
        if ($oInput->getPost('action') === 'comment') {
            $this->aData[\Defines\RequestMethod::POST][\Defines\Extension::JSON] = array(
                'action' => array('list'=> ['comment']),
                'content' => array('min_length' => 3),
                'mark' => array('list' => ['', 'votes_up', 'votes_down'])
            );
        }
        return new ArrayUndef($this->aData);
    }

    /**
     * Check access
     *
     * @param string $sRequestMethod - const from \Defines\RequestMethod
     * @param string $sExt - const from \Defines\Extension
     * @return \Access\Allowed
     * @throws \Error\Validation
     */
    public function isReach($sRequestMethod, $sExt)
    {
        $oInput = new \Engine\Request\Input();
        $aData = $this->checkExtraRequests($oInput);
        $oTranslate = \System\Registry::translation();
        // Check if request is missing
        if ($aData[$sRequestMethod] instanceof ArrayUndef) {
            throw new \Error\Validation($oTranslate->sys('LB_HEADER_405'), Code::E_NOT_ALLOWED);
        }
        // AMP extension is not needed to be defined
        if ($sExt === \Defines\Extension::AMP) {
            $sExt = \Defines\Extension::HTML;
        }
        // Check responses
        if (!array_key_exists($sExt, $aData[$sRequestMethod])) {
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_RESPONSE_TYPE'), Code::E_NOT_ACCEPTABLE);
        }

        if ($sRequestMethod === \Defines\RequestMethod::GET) {
            $aReqData = $oInput->getGet(null, null, FILTER_DEFAULT);

        } else {
            $aReqData = $oInput->getPost(null, null, FILTER_DEFAULT);
            if (isset($aData[\Defines\RequestMethod::GET][$sExt])) {
                $this->checkList($oInput->getGet(null, null, FILTER_DEFAULT), $aData[\Defines\RequestMethod::GET][$sExt]);

            } elseif (sizeof($oInput->getGet())) {
                throw new \Error\Validation($oTranslate->sys('LB_ERROR_MANY_PARAMETERS'), Code::E_NOT_ACCEPTABLE);
            }
        }

        if ($this->aReqData) {
            $a = $oInput->getParam();
            foreach ($this->aReqData as $key) {
                if (!array_key_exists($key, $a)) {
                    throw new \Error\Validation(sprintf($oTranslate->sys('LB_ERROR_MANDATORY'), $key), Code::E_NOT_ACCEPTABLE);
                }
            }
        }

        $this->checkList($aReqData, $aData[$sRequestMethod][$sExt]);
        return $this;
    }

    /**
     * Validate the list
     *
     * @param array $aList
     * @param array $aAllowed
     */
    protected function checkList($aList, $aAllowed)
    {
        // If array is empty - it's not needed to check permissions
        if (!$aList || is_null($aAllowed)) {
            return;
        }
        $oTranslate = \System\Registry::translation();
        // If input size if bigger - 100% error
        if (sizeof($aList) > sizeof($aAllowed)) {
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_MANY_PARAMETERS'), Code::E_NOT_ACCEPTABLE);
        }
        // Verify the list
        foreach ($aList as $sKey => $mValue) {
            // Is check only existance without any rules
            if (array_key_exists($sKey, $aAllowed) && is_null($aAllowed[$sKey])) {
                continue;
            }
            // Missing in a list
            if (!isset($aAllowed[$sKey])) {
                if (strpos($sKey, '/') === 0) {
                    $sKey = "URL[$sKey]";
                }
                throw new \Error\Validation($oTranslate->sys('LB_HEADER_429') . ': '. htmlspecialchars($sKey), Code::E_NOT_ALLOWED);
            }
            // Check values rules
            if (!$this->checkValue($mValue, $aAllowed[$sKey])) {
                throw new \Error\Validation(sprintf(
                    $oTranslate->sys('LB_ERROR_VALIDATION'),
                    htmlspecialchars($sKey),
                    $this->getError()
                ));
            }
        }
    }

    /**
     * Define error message
     *
     * @param string $mssg
     * @param string $param
     * @return boolean
     */
    public function setError($mssg, $param = '')
    {
        $this->error = sprintf($mssg, (string) $param);
        return true;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Check validity
     *
     * @param type $mValue
     * @param type $aParams
     * @return boolean
     */
    protected function checkValue($mValue, $aParams)
    {
        $c = new \System\Converter\StringUtf();
        $t = \System\Registry::translation();
        $valid = true;
        if (isset($aParams['ctype'])) {
            $mCopy = $mValue;
            settype($mCopy, $aParams['ctype']);
        }
        if (
                isset($aParams['type']) && gettype($mValue) !== $aParams['type'] && $this->setError($t->sys('LB_ERROR_VALUE_TYPE'))
                || isset($aParams['ctype']) && (string)$mCopy !== (string)$mValue && $this->setError($t->sys('LB_ERROR_VALUE_TYPE'))
                || isset($aParams['min_length']) && $c->strlen($mValue) < $aParams['min_length'] && $this->setError($t->sys('LB_ERROR_VALUE_MIN_LENGTH'), $aParams['min_length'])
                || isset($aParams['max_length']) && $c->strlen($mValue) > $aParams['max_length'] && $this->setError($t->sys('LB_ERROR_VALUE_MAX_LENGTH'), $aParams['max_length'])
                || isset($aParams['min']) && $mValue < $aParams['min'] && $this->setError($t->sys('LB_ERROR_VALUE_MIN'), $aParams['min'])
                || isset($aParams['max']) && $mValue > $aParams['max'] && $this->setError($t->sys('LB_ERROR_VALUE_MAX'), $aParams['max'])
                || isset($aParams['keys']) && array_diff(array_keys($mValue), array_intersect(array_keys((array)$mValue), $aParams['keys'])) && $this->setError($t->sys('LB_ERROR_VALUE_KEY'))
                || isset($aParams['list']) && !in_array($mValue, $aParams['list'], true) && $this->setError($t->sys('LB_ERROR_VALUE_LIST'))
                || isset($aParams['array_list']) && array_diff($mValue, $aParams['array_list']) && $this->setError($t->sys('LB_ERROR_VALUE_SCOPE'))
                || isset($aParams['pattern']) && !preg_match($aParams['pattern'], $mValue) && $this->setError($t->sys('LB_ERROR_VALUE_PATTERN'), $aParams['pattern'])
                || isset($aParams['sanitize']) && filter_var($mValue, $aParams['sanitize']) !== $mValue && $this->setError($t->sys('LB_ERROR_VALUE_SANITIZE'))
        ) {
            $valid = false;
        }
        return $valid;
    }
}
