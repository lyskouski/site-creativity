<?php namespace Engine\Validate\Helper;

/**
 * Filter massive for Sanitize object
 *
 * $iFilterType =
 * // Sanitizing will remove any illegal character from the data
 *   FILTER_SANITIZE_EMAIL
 *   FILTER_SANITIZE_ENCODED
 *   FILTER_SANITIZE_FULL_SPECIAL_CHARS
 * * FILTER_SANITIZE_MAGIC_QUOTES
 *   FILTER_SANITIZE_NUMBER_FLOAT
 *   FILTER_SANITIZE_NUMBER_INT
 *   FILTER_SANITIZE_SPECIAL_CHARS
 *   FILTER_SANITIZE_STRING
 *   FILTER_SANITIZE_STRIPPED
 *   FILTER_SANITIZE_URL
 * // Validating will determine if the data is in proper form
 *   FILTER_VALIDATE_BOOLEAN
 *   FILTER_VALIDATE_EMAIL
 *   FILTER_VALIDATE_FLOAT
 *   FILTER_VALIDATE_INT
 *   FILTER_VALIDATE_IP
 *   FILTER_VALIDATE_URL
 *
 * @since 2015-06-18
 * @author Viachaslau Lyskouski
 */
class Filter extends \ArrayObject
{

    /**
     * Add filter type for a single value with defined key
     *
     * @param string $sKey
     * @param integer $iFilterType
     *
     *
     * @return \Engine\Tools\Filter
     */
    public function addSingle($sKey, $iFilterType = FILTER_SANITIZE_MAGIC_QUOTES)
    {
        $this[$sKey] = $iFilterType;
        return $this;
    }

    /**
     * Add filter for an array with defined key
     *
     * @param string $sKey
     * @param integer $iFilterType
     * @return \Engine\Tools\Filter
     */
    public function addArray($sKey, $iFilterType = FILTER_SANITIZE_MAGIC_QUOTES)
    {
        $this[$sKey] = array(
            'filter' => $iFilterType,
            'flags' => FILTER_FORCE_ARRAY
        );
        return $this;
    }
}
