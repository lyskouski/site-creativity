<?php namespace Access\Filter;

/**
 * Description of View
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
class FilterList
{
    public function get()
    {
        $list = array();
        /* @var $filter \Access\Filter\FilterAbstract */
        foreach (func_get_args() as $filter) {
            foreach ($filter->get() as $key => $value) {
                $list[$key] = $value;
            }
        }
        return $list;
    }
}
