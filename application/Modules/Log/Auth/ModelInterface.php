<?php namespace Modules\Log\Auth;

interface ModelInterface {

    /**
     * Get error message
     * @return string
     */
    public function getMessage();

    /**
     * Get the result code of Authentication
     * @return integer
     */
    public function getCode();
}