<?php namespace Access\Filter;

/**
 * Description of FilterAbstract
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
abstract class FilterAbstract
{
    protected $list = array();

    public function get()
    {
        return $this->list;
    }
}
