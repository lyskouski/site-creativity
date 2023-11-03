<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of Missing
 *
 * @author slaw
 */
class Missing implements TexInterface
{

    protected $content = '';

    public function __construct($content)
    {
        if (!in_array($content, ['begin', 'end'])) {
            $this->content = $content;
        }
    }

    public function bind($content)
    {
        $this->content .= $content;
        return $this;
    }

    public function clear()
    {
        $this->content = '';
        return $this;
    }

    public function initial()
    {
        return $this->content;
    }

    /**
     * @note has to overriden
     * @return string
     */
    public function get()
    {
        $err = \System\Registry::translation()->sys('LB_HEADER_404');
        return "[$err {{$this->content}}]";
    }

}
