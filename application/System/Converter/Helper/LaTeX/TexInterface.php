<?php namespace System\Converter\Helper\LaTeX;

/**
 * Description of TexInterface
 *
 * @author slaw
 */
interface TexInterface
{
    /**
     * Init object
     * @param string $content
     */
    public function __construct($content);

    /**
     * Add content
     * @param string $content
     */
    public function bind($content);

    /**
     * Clear content
     * @return TexInterface
     */
    public function clear();

    /**
     * Get result
     * @return string
     */
    public function get();
}
