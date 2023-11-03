<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Unique
 *
 * @author s.lyskovski
 */
class Unique extends Missing
{
    public function __construct($content)
    {
        // do nothing, exclude first content
    }

    public function get()
    {
        $a = array_map('trim', explode(',', $this->content));
        return implode(',', array_unique($a));
    }
}
