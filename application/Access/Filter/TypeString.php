<?php namespace Access\Filter;

/**
 * Validate string
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
class TypeString extends FilterAbstract
{
    public function __construct($min = null, $max = null)
    {
        $this->list['sanitize'] = FILTER_SANITIZE_STRING;
        // Check min
        if (!is_null($min)) {
            $this->list['min_length'] = $min;
        }
        // Check max
        if (!is_null($max)) {
            $this->list['max_length'] = $max;
        }
    }
}
