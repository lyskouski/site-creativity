<?php namespace Engine\Request;

/**
 * Decode JSON input into array statement
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Engine/Request
 */
class Json extends \ArrayObject
{

    /**
     * @var boolean - json-string is valid
     */
    protected $json = true;

    /**
     * Init array from from json-string
     *
     * @param string $field
     */
    public function __construct($field)
    {
        $value = null;
        if (is_string($field)) {
            $value = json_decode($field, true);
        }
        if (json_last_error() == JSON_ERROR_NONE && is_array($value)) {
            parent::__construct($value);
        } else {
            $this->json = false;
        }
    }

    /**
     * Check if array is JSON
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->json;
    }

    /**
     * Alias of getArrayCopy
     *
     * @return array
     */
    public function getData()
    {
        return $this->getArrayCopy();
    }
}
