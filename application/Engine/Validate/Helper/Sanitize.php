<?php namespace Engine\Validate\Helper;

/**
 * Filter values or trigger an error if it's not compliant
 * @note [!] Take care about the differencies between FILTER_(SANITIZE|VALIDATE)_* types
 *
 * @since 2015-06-18
 * @author Viachaslau Lyskouski
 */
class Sanitize
{

    protected $bRestriction = false;

    /**
     *
     * @var \Engine\Tools\Filter
     */
    protected $oFilter;

    /**
     * Init sanitize with defined type of validation
     *
     * @param boolean $bTriggerError
     */
    public function __construct($bTriggerError = false, Filter $oFilter = null)
    {
        $this->bRestriction = (boolean) $bTriggerError;
        $this->oFilter = $oFilter;
    }

    /**
     * Generate filter statement for the input array
     *
     * @param array $aValues
     * @param integer $iFilterType
     * @return array
     */
    public function generateFilter($aValues, $iFilterType)
    {
        $oFilter = new Filter();
        // Associative array
        // if ( array_values( $aValue ) !== $aValue )
        // Sequential array
        // else ...
        foreach ($aValues as $sKey => $mValue) {
            if (is_array($mValue)) {
                $oFilter->addArray($sKey, $iFilterType);
            } else {
                $oFilter->addSingle($sKey, $iFilterType);
            }
        }
        return $oFilter;
    }

    /**
     * Filter or validate
     * @note depence on object's initialisation parameter
     *
     * @param mixed $mValue
     * @param integer $iFilterType
     * @return mixed
     */
    public function get($mValue, $iFilterType = FILTER_SANITIZE_MAGIC_QUOTES)
    {
        if ($this->bRestriction) {
            $mResult = $this->truth($mValue, $iFilterType);
        } else {
            $mResult = $this->filter($mValue, $iFilterType);
        }
        return $mResult;
    }

    /**
     * Filter value
     * @todo filter value
     *
     * @param mixed $mValue
     * @param integer $iFilterType
     */
    public function filter($mValue, $iFilterType = FILTER_SANITIZE_MAGIC_QUOTES)
    {
        if (is_null($mValue)) {
            return $mValue;
        }

        // To be sure that validation will not change the type of checked value
        // $sType = gettype( $mValue );
        // Check if the array filtering is required
        if (is_array($mValue)) {
            $oFilter = $this->oFilter;
            if (is_null($oFilter)) {
                $oFilter = $this->generateFilter($mValue, $iFilterType);
            }
            $mReturn = array();
            foreach ($mValue as $mKey => $mValue) {
                $mReturn[$mKey] = filter_var($mValue, $oFilter[$mKey]);
            }
        }
        // Single value filter
        else {
            $mReturn = filter_var($mValue, $iFilterType);
        }
        // settype($mReturn, $sType);
        return $mReturn;
    }

    /**
     * Validate value
     * @todo validate value
     *
     * @param mixed $mValue
     * @param integer $iFilterType
     */
    public function truth($mValue, $iFilterType = FILTER_SANITIZE_MAGIC_QUOTES)
    {
        $val = $this->filter($mValue, $this->convertFilterType($iFilterType));
        if ($val !== (new Quote)->convert($mValue)) {
            throw new \Error\Validation('Invalid value was taken from request!');
        }
        return $val;
    }

    /**
     * Convert filter type for a `truth` validation
     *
     * @param integer $iFilterType
     * @return integer
     */
    protected function convertFilterType($iFilterType)
    {
        switch ($iFilterType) {
            case FILTER_SANITIZE_NUMBER_INT:
                $iFilter = FILTER_VALIDATE_INT;
                break;

            case FILTER_SANITIZE_NUMBER_FLOAT:
                $iFilter = FILTER_VALIDATE_FLOAT;
                break;

            case FILTER_SANITIZE_EMAIL:
                $iFilter = FILTER_VALIDATE_EMAIL;
                break;

            case FILTER_SANITIZE_URL:
                $iFilter = FILTER_VALIDATE_URL;
                break;

            default:
                $iFilter = $iFilterType;
        }
        return $iFilter;
    }
}
