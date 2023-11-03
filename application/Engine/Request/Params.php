<?php namespace Engine\Request;

use Engine\Validate\Common;
use Engine\Request\Input\Server;

/**
 * Central class for site params
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Engine\Request
 */
class Params
{

    /**
     * Validation object
     * @var \Engine\Validate\Common
     */
    protected $oValidate;

    /**
     * Application language
     * @var string
     */
    protected $sLanguage;

    /**
     * Response type
     * @var string
     */
    protected $sResponseType;

    /**
     * Controller name
     * @var string
     */
    protected $sModuleName;

    /**
     * REquest type
     * @var string
     */
    protected $sRequestMethod;

    /**
     * Request params
     * @var array
     */
    protected $aParams;

    /**
     * @var boolean
     */
    protected $bStrict;

    /**
     * Params instance
     * @param boolean $bStrict - throw excetion if something is wrong
     */
    public function __construct($bStrict = true)
    {
        $this->oValidate = new Common();
        $this->bStrict = $bStrict;
        $this->identifyParams();
    }

    protected function identifyParams()
    {
        $oUrlHelper = new Helper\Url();
        $this->setLanguage($oUrlHelper->getLanguage());
        $this->setResponseType($oUrlHelper->getResponseType());
        $this->setParams($oUrlHelper->getParams($this->bStrict));

        $this->setRequestMethod((new Server)->getRequestMethod());
        $this->setModuleName($oUrlHelper->getModuleName());
    }

    public function setModuleName($sModuleName)
    {
        $this->sModuleName = $sModuleName;
    }

    public function getModuleName()
    {
        return $this->sModuleName;
    }

    public function getModuleUrl()
    {
        return trim(strtolower(str_replace(array('\\', 'Modules'), '/', $this->sModuleName)), '/');
    }

    public function setLanguage($mValue)
    {
        if (!$mValue) {
            $mValue = \Defines\Language::getDefault();
        }
        $this->sLanguage = $this->oValidate->getLanguage($mValue, $this->bStrict);
    }

    public function getLanguage()
    {
        return $this->sLanguage;
    }

    /**
     * Set response type
     * @see \Defines\Extension
     *
     * @param string $mValue
     */
    public function setResponseType($mValue)
    {
        if (!$mValue) {
            $mValue = \Defines\Extension::getDefault();
        }
        $this->sResponseType = $this->oValidate->getExtension($mValue, $this->bStrict);
    }

    /**
     * Get response type
     * @see \Defines\Extension
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->sResponseType;
    }

    public function setParams($mValue)
    {
        $this->aParams = $mValue;
    }

    public function setParam($key, $mValue)
    {
        $this->aParams[$key] = $mValue;
    }


    public function getParams()
    {
        return $this->aParams;
    }

    public function getParam($key)
    {
        if (array_key_exists($key, $this->aParams)) {
            return $this->aParams[$key];
        }
    }

    public function setRequestMethod($mValue)
    {
        $this->sRequestMethod = $this->oValidate->getRequestMethod($mValue, $this->bStrict);
    }

    public function getRequestMethod()
    {
        return $this->sRequestMethod;
    }
}
