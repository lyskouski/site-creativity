<?php namespace Engine\Validate;

/**
 * Validate common values: MODE_CODE, LANG, etc
 *
 * @since 2015-07-06
 * @author Viachaslau Lyskouski
 */
class Common
{

    /**
     * Generic validate functionality
     *
     * @param \Defines\ListInterface $oClass
     * @param mixed $mValue
     * @param boolean $bTriggerError
     * @param string $sErrorMessage
     * @return mixed
     * @throws \Error\Validation
     */
    public function validate(\Defines\ListInterface $oClass, $mValue, $bTriggerError, $sErrorMessage = 'Validation failed')
    {
        $mResult = $oClass::getDefault();
        $aList = $oClass::getList();
        $sType = gettype(current($aList));
        if (in_array(settype($mValue, $sType), $aList)) {
            $mResult = $mValue;
        } elseif ($bTriggerError) {
            throw new \Error\Validation($sErrorMessage);
        }
        return $mResult;
    }

    /**
     * Validate Logger
     *
     * @param string $mValue
     * @param boolean $bTriggerError
     * @return string
     * @throws \Error\Validation
     */
    public function getLogger($mValue, $bTriggerError = false)
    {
        return $this->validate(new \Defines\Logger, $mValue, $bTriggerError, 'Incorrect log priority');
    }

    /**
     * Validate LANG
     *
     * @param string $mValue
     * @param boolean $bTriggerError
     * @return string
     * @throws \Error\Validation
     */
    public function getLanguage($mValue, $bTriggerError = false)
    {
        return $this->validate(new \Defines\Language, $mValue, $bTriggerError, 'Incorrect language');
    }

    /**
     * Validate LANG
     *
     * @param string $mValue
     * @param boolean $bTriggerError
     * @return string
     * @throws \Error\Validation
     */
    public function getExtension($mValue, $bTriggerError = false)
    {
        return $this->validate(new \Defines\Extension, $mValue, $bTriggerError, 'Incorrect extension type');
    }

    /**
     * Validate LANG
     *
     * @param string $mValue
     * @param boolean $bTriggerError
     * @return string
     * @throws \Error\Validation
     */
    public function getRequestMethod($mValue, $bTriggerError = false)
    {
        return $this->validate(new \Defines\RequestMethod, $mValue, $bTriggerError, 'Incorrect request method type');
    }

    /**
     * Validate APPLICATION_ENV
     *
     * @param string $mValue
     * @param boolean $bTriggerError
     * @return string
     * @throws \Error\Validation
     */
    public function getEnvironment($mValue, $bTriggerError = false)
    {
        return $this->validate(new \Defines\ServerType, $mValue, $bTriggerError, 'Internal error: Incorrect Environment Type');
    }
}
