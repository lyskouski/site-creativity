<?php namespace Modules\Person\Work\Prose;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\Person\Work\Article\Controller
{

    /**
     * Has to be declared for other classes
     * @return Model
     */
    protected function getModel()
    {
        return (new Model);
    }

}
