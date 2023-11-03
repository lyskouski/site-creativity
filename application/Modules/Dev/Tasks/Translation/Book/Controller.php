<?php namespace Modules\Dev\Tasks\Translation\Book;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Dev/Tasks
 */
class Controller extends \Modules\Dev\Tasks\Translation\Text\Controller
{

    public function initModel()
    {
        $this->model = new Model();
    }

}
