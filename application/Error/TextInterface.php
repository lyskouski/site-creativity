<?php namespace Error;

/**
 * Exception interface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
interface TextInterface
{
    /**
     * Get link to an error description template
     *
     * @return string
     */
    public function getTemplateName ();

    /**
     * Plot HTML response
     */
    public function plotErrorPage();

}
