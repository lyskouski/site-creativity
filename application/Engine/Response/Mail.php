<?php namespace Engine\Response;

/**
 * Email notification
 */
class Mail
{

    /**
     * @var \Swift_SmtpTransport
     */
    protected $smtp;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $failed;

    /**
     * Init smtp connection
     * @todo mail sender
     */
    public function __construct()
    {
        $params = \System\Registry::config()->getMailPush();
        $support = \System\Registry::translation()->sys('LB_MAIL_SUPPORT');
        $this->from = "$support <{$params['username']}>";

        $this->smtp = (new \Mail)->factory(
            'smtp',
            array(
                'host' => $params['host'],
                'port' => $params['port'],
                'auth' => true,
                'username' => $params['username'],
                'password' => $params['password']
            )
        );
    }

    /**
     * @todo Send mail
     *
     * @param string $mailto
     * @param string $subject
     * @param string $html
     * @param string $reply
     * @return boolean
     * @throws \Error\Application
     */
    public function sendMail($mailto, $subject, $html, $reply = '')
    {
        $message = str_replace(array("\r", "\n"), array(), $html);
        $text = strip_tags($message);

        $mime_params = array(
            'text_encoding' => '7bit',
            'text_charset' => 'UTF-8',
            'html_charset' => 'UTF-8',
            'head_charset' => 'UTF-8'
        );
        $mime = new \Mail_mime();
        $mime->setTXTBody($text);
        $mime->setHTMLBody($message);
        $mime->addAttachment(__DIR__ .'/Mail/logo.png',
            'application/octet-stream',
            '',
            true,
            'base64',
            'attachment',
            '',
            '',
            '',
            null,
            null,
            '',
            null,
            array(
                'Content-ID' => '<logo.png>'
            )
        );

        $body = $mime->get($mime_params);

        $aHeaders = array();
        if ($reply) {
            $aHeaders['In-Reply-To'] = $reply;
            $aHeaders['References'] = $reply;
            $aHeaders['Delivered-To'] = $this->from;
        }
        if (strpos($subject, '?=') && strpos(" $subject", '=?') === 1) {
            $aHeaders['Subject'] = $subject;
        } else {
            $aHeaders['Subject'] = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        }
        $aHeaders['From'] = $this->from;
        $aHeaders['To'] = $mailto;
        if (!strpos($mailto, '>')) {
            $aHeaders['To'] = "<$mailto>";
        }

        $headers = $mime->headers($aHeaders);

        return $this->smtp->send($aHeaders['To'], $headers, $body);
    }

    /**
     * @todo Get error message
     *
     * @return string
     */
    public function getError()
    {
        return \System\Registry::translation()->sys('LB_ERROR_MAIL_TO');
    }

}
