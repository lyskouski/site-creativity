<?php namespace Modules\Cron\Notify;

/**
 * Notification controller
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Cron/Notify
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML);

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Notification main page
     * @todo finalise visual aspects
     *
     * @param array $aParams
     * @return \Layouts\Helper\Zero
     */
    public function indexAction(array $aParams)
    {
        return new \Layouts\Helper\Zero($this->request, $this->response);
    }

    public function whatAction()
    {
        return;
        $api = \System\Registry::config()->getSocialApi('whatsup');
        $w = new \WhatsProt($api['login'], $api['nickname'], \System\Registry::config()->getDevMode());
        $w->connect();
        var_dump($w->sendMessage($api['login'], 'Guess the number :)'));

        //send picture
        //$w->sendMessageImage($target, 'demo/x3.jpg');
        //send video
        //$w->sendMessageVideo($target, 'http://techslides.com/demos/sample-videos/small.mp4');
        //send Audio
        //$w->sendMessageAudio($target, 'http://www.kozco.com/tech/piano2.wav');
        //send Location
        //$w->sendMessageLocation($target, '4.948568', '52.352957');
        //$w = new \Registration($username, $token, $nickname, false);
        //step 1: $w->codeRequest('sms');
        //step 2: $w->codeRegister('250-218');

        $msgs = $w->getMessages();
        foreach ($msgs as $m) {
            print($m->NodeString("") . "\n");
        }
        die;
    }

    /**
     * Send mails
     *
     * @param array $aParams
     * @return \Layouts\Helper\Zero
     */
    public function mailAction(array $aParams)
    {
        if (php_sapi_name() != 'cli') {
            throw new \Error\Application('This application must be run on the command line.');
        }

        $oSendMail = new \Engine\Response\Mail();
        $oHelper = new \Data\CronHelper();

        $aData = $oHelper->getMailTasks();
        /* @var $oMail \Data\Doctrine\Main\CronTaskMail */
        foreach ($aData as $oMail) {
            $bSend = $oSendMail->sendMail(
                $oMail->getMailto(),
                $oMail->getTopic(),
                $oMail->getContent(),
                $oMail->getReplyTopic()
            );
            $oMail->setStatus($bSend);
            if (!$bSend) {
                $oMail->setErrors($oSendMail->getError());
            }
            $oHelper->getEntityManager()->persist($oMail);
        }
        $oHelper->getEntityManager()->flush();

        return new \Layouts\Helper\Zero($this->request, $this->response);
    }
}
