<?php namespace Access\Validate;

/**
 * Description of View
 *
 * @since 2016-01-31
 * @author Viachaslau Lyskouski
 */
class View extends Check
{
    public function getType() {
        return \Defines\User\Access::READ;
    }
}
