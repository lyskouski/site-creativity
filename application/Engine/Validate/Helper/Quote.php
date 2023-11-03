<?php namespace Engine\Validate\Helper;

/**
 * Description of Quote
 *
 * @since 2015-06-18
 * @author Viachaslau Lyskouski
 */
class Quote
{

    /**
     * Adapt quotes
     *
     * @param mixed $mValue
     * @return mixed
     */
    public function convert($mValue)
    {
        if (is_array($mValue)) {
            foreach ($mValue as &$val) {
                $val = $this->convert($val);
            }
        } elseif (is_string($mValue)) {
            $mValue = str_replace("'", '&#39;', $mValue);
        }
        return $mValue;
    }
}
