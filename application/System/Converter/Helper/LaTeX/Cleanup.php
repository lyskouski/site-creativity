<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Cleanup
 *
 * @author s.lyskovski
 */
class Cleanup extends Missing
{
    public function __construct($content)
    {
        // do nothing, exclude first content
    }

    public function get()
    {
        return str_replace(["\n", "\t", '"'], [' ', ' ', "'"], strip_tags($this->content));
    }
}
