<?php namespace Modules\Cron;

/**
 * Notification controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Cron/Notify
 */
abstract class AbstractController extends \Modules\AbstractController
{
    protected function initAllowed()
    {
        if (!\System\Registry::cron()) {
            throw new \Error\Application('Command line only.');
        }
    }

    public function indexAction(array $aParams)
    {
        return new \Layouts\Helper\Zero($this->request, $this->response);
    }
}