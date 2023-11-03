<?php namespace System\Converter;

/**
 * Description of Number
 *
 * @author slaw
 */
class Number
{

    public function getFloat($number, $decimals = 0)
    {
        $locale = localeconv();
        if (!$locale['thousands_sep']) {
            $locale['thousands_sep'] = "'";
        }
        return number_format($number, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
    }

    public function getIncrement($number, $decimals = 0)
    {
        $val = $this->getFloat($number, $decimals);
        if ($val === '0') {
            $val = "=$val";
        } elseif ($val && $val[0] !== '-') {
            $val = "+$val";
        }
        return $val;
    }

    public function getBytes($string)
    {
        preg_match('/(?<value>\d+)(?<option>.?)/i', trim($string), $matches);
        $inc = array(
            'g' => 1073741824, // (1024 * 1024 * 1024)
            'm' => 1048576, // (1024 * 1024)
            'k' => 1024
        );

        $value = (int) $matches['value'];
        $key = strtolower(trim($matches['option']));
        if (array_key_exists($key, $inc)) {
            $value *= $inc[$key];
        }

        return $value;
    }

}
