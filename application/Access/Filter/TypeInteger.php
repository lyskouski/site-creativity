<?php namespace Access\Filter;

/**
 * Validate string
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
class TypeInteger extends FilterAbstract
{
    public function __construct($min = null, $max = null)
    {
        $this->list['ctype'] = 'integer';
        // Check min
        if (!is_null($min)) {
            $this->list['min'] = $min;
        }
        // Check max
        if (!is_null($max)) {
            $this->list['max'] = $max;
        }
    }
}
