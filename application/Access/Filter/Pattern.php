<?php namespace Access\Filter;

/**
 * Validate string
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
class Pattern extends FilterAbstract
{
    public function __construct($pattern = null)
    {
        if ($pattern) {
            $this->list['pattern'] = $pattern;
        }
    }

    /**
     * Get publication pattern
     *
     * @return \Access\Filter\Pattern
     */
    public function publication()
    {
        $this->list['pattern'] = '/^i\d{1,}$/';
        return $this;
    }

    public function date() {
        $this->list['pattern'] = '/^\d{4,4}\-\d{2,2}\-\d{2,2}$/';
        return $this;
    }
}
