<?php namespace Error;

/**
 * Trigger validation error
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
class Validation extends TextAbstract
{

    public function getTemplateName ()
    {
        return '\Error\Validation';

    }
}
