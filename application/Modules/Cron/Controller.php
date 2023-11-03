<?php namespace Modules\Cron;

/**
 * Notification controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Cron/Notify
 */
class Controller extends AbstractController
{

    public function indexAction(array $aParams)
    {
        $time = new \DateTime();
        $params = new \Engine\Request\Params();

        // Beginning of a day
        if ($time->format('H:i') === '00:00') {
            (new Task\Book\Controller($params))->dailyAction($aParams);
            echo "CR Crone: Daily\n";
        }
        // Beginning of a month
        if ($time->format('d H:i') === '01 00:00') {
            (new Task\Book\Controller($params))->monthlyAction($aParams);
            echo "CR Crone: Monthly\n";
        }

        return new \Layouts\Helper\Zero($this->request, $this->response);
    }

}
