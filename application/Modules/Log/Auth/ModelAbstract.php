<?php namespace Modules\Log\Auth;

use \Defines\Response\Code;

/**
 * Abstract model to describe the common behaviour for all Authentication types
 *
 * @since 2015-09-30
 * @author Viachaslau Lyskouski
 * @package Modules/Log/Auth
 */
abstract class ModelAbstract extends \Modules\AbstractModel implements ModelInterface
{

    /**
     * @note \System\Registry::translation()->sys('LB_ERROR_MISSING_DATA');
     * @var string
     */
    protected $sMessage = 'LB_ERROR_MISSING_DATA';

    /**
     * By default fatal error
     * @var integer
     */
    protected $iCode = Code::E_FATAL;

    /**
     * Field number
     * @var string
     */
    protected $sField = '';

    /**
     *
     * @return
     */
    public function getMessage()
    {
        $res = $this->sMessage;
        if (strtoupper($res) === $res) {
            $res = \System\Registry::translation()->sys("{$this->sMessage}");
        }
        return $res;
    }

    /**
     * Set result of Authentication
     * @note without attributes it will set the result as successfull
     *
     * @param string $sError - transaltion identificator
     * @param integer $iCode
     * @param string $sField
     */
    protected function updateResult($sError = '', $iCode = Code::E_OK, $sField = '')
    {
        $this->sMessage = $sError;
        $this->iCode = $iCode;
        $this->sField = $sField;
    }

    /**
     * Get result code
     * @see \Defines\Response\Code
     * @return integer
     */
    public function getCode()
    {
        return $this->iCode;
    }

    /**
     * Return the field number that failed
     * @return string
     */
    public function getField() {
        return $this->sField;
    }
}
