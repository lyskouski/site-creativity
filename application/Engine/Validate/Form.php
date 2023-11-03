<?php namespace Engine\Validate;

/**
 * Validate basic parameters from request
 * @see ValidateInterface
 *
 * @since 2015-07-06
 * @author Viachaslau Lyskouski
 */
class Form implements ValidateInterface
{

    public function sanitize($aValues, $iFilterType = FILTER_DEFAULT)
    {
        // to fix a bug with convertation int to string for FILTER_DEFAULT
        if ($iFilterType === FILTER_DEFAULT) {
            return $aValues;
        }
        foreach (array_keys($aValues) as $sKey) {
            if (is_numeric($sKey)) {
                throw new \Error\Validation('Numeric parameters are not allowed');
            }
        }
        $oFilter = (new Helper\Sanitize)->generateFilter($aValues, $iFilterType);
        foreach ($oFilter as $sKey => $mFilterType) {
            $oFilter[$sKey] = $this->updateValitationType(strtoupper($sKey), $mFilterType);
        }
        return filter_var_array($aValues, (array) $oFilter);
    }

    public function updateValitationType($sUpperKey, $mFilterType)
    {
        switch ($sUpperKey) {
            //case '':
            //    $mFilterType = FILTER_SANITIZE_STRING;
            //    break;
            //case '':
            //    $mFilterType = FILTER_VALIDATE_INT;
            //    break;
            // Any validation should be excluded - it's an internal requests with XML data
            //case '':
            //    $mFilterType = FILTER_DEFAULT;
            //    break;
        }
        return $mFilterType;
    }

    public function isBase64($string)
    {
        $decoded = base64_decode($string, true);
        switch (true) {
            case !preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string):
            case !base64_decode($string, true):
            case base64_encode($decoded) != $string:
                $valid = false;
                break;

            default:
                $valid = true;

        }

        return $valid;
    }
}
