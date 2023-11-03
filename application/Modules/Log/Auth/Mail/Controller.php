<?php namespace Modules\Log\Auth\Mail;

use Engine\Response\Meta\Script;

/**
 * General controller for email Authentication
 * @see \Modules\AbstractController
 *
 * Registry logic:
 * 1. Validate mail and password length
 * 1.1. AES crypt is used on front-end to send password
 * 2. Create a row in `user_account` (linked to a nullable-user[id=0])
 * 2.1. Pssw is used for AES crypt as a key
 * 3. Put mail into garbage collector - cron_task_mail
 * 3.1. Send mail by cron
 * 4. Forward to \Modules\Log\Auth\Controller to enter username and requested token
 *
 * Authentication logic:
 * 1. AES crypt password
 * 2. Descrypt password and use it for decryption of the database data
 * 3. Compare decrypted value with `updated_at`-field
 *
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\AbstractController
{

    /**
     * @var Model
     */
    protected $oModel;

    /**
     * @var \Layouts\Helper\Login
     */
    protected $oHelper;

    /**
     * @var string
     */
    protected $sMail;

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
            ->bindExtension(\Defines\Extension::JSON)
                ->bindKey('email')
                ->bindKey('pssw')
                ->bindKey('pssw_retry')
                ->bindKey('account')
                ->bindKey('token')
                ->bindKey('action');
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * Init common parameters for all actions
     */
    public function bindParams()
    {
        $this->oModel = new Model();

        $this->oHelper = new \Layouts\Helper\Login($this->request, $this->response);

        $this->sMail = $this->input->getPost('email', '', FILTER_VALIDATE_EMAIL);
    }

    /**
     * User Authentication
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        $this->bindParams();
        $bResult = $this->oModel->login(
            $this->sMail,
            $this->input->getPost('pssw', '', FILTER_DEFAULT)
        );
        // if registration was not finished -> show registry page
        if (is_null($bResult)) {
            $this->registryAction($aParams);

        } elseif ($bResult) {
            $this->response->meta(new Script('', "Vaviorka.registry.trigger('Request/Share', 'submit', ['{$this->input->getCookie(\Access\User::COOKIE_AUTH)}']);"));

        } else {
            $this->sendErrors();
        }

        return $this->oHelper;
    }

    /**
     * Create new user
     *
     * @param array $aParams
     */
    public function registryAction(array $aParams)
    {
        $this->bindParams();
        $bResult = $this->oModel->registry(
            $this->sMail,
            $this->input->getPost('pssw', '', FILTER_DEFAULT)
        );

        if ($bResult) {
            $aData = array(
                'account' => $this->oModel->getMessage(),
                'type' => \Defines\User\Account::MAIL,
                // needed a value from the mail
                'token' => ''
            );
            return $this->forward('Log\Auth', 'index', $aData);
        } else {
            $this->sendErrors();
        }
        return $this->oHelper;
    }

    /**
     * Restore password
     *
     * @param array $aParams
     */
    public function restoreAction(array $aParams)
    {
        $this->bindParams();
        if (!$this->oModel->restore($this->sMail)) {
            $this->sendErrors();
        } else {
            $this->oHelper->add('restore', array('email' => $this->oModel->getMessage()));
        }
        return $this->oHelper;
    }

    /**
     * Change password
     *
     * @param array $aParams
     */
    public function changeAction(array $aParams)
    {
        $this->bindParams();
        $sPssw = $this->input->getPost('pssw', '', FILTER_DEFAULT);
        $sPssw2 = $this->input->getPost('pssw_retry', '', FILTER_DEFAULT);
        $sToken = $this->input->getPost('token', '', FILTER_SANITIZE_STRING);
        if (!$this->oModel->changePssw($this->sMail, $sToken, $sPssw, $sPssw2)) {
            $this->sendErrors('restore');
        } else {
            $this->oHelper = $this->indexAction($aParams);
        }


        return $this->oHelper;
    }

    /**
     * Add to response error
     */
    protected function sendErrors($sTemplate = null)
    {
        $this->oHelper->add(
            'Basic/null',
            array(
                \Error\TextAbstract::E_MESSAGE => $this->oModel->getMessage(),
                \Error\TextAbstract::E_CODE => $this->oModel->getCode()
            )
        );

        $aParams = array(
            'email' => $this->sMail,
            'error' => array(
                'text' => $this->oModel->getMessage(),
                'field' => $this->oModel->getField()
            )
        );

        $sDir = null;
        if (is_null($sTemplate)) {
            $sTemplate = 'login/mail';
            $sDir = realpath(__DIR__ . '/../..' . \Engine\Response\Template::VIEW_FOLDER);
        }
        $this->oHelper->add($sTemplate, $aParams, $sDir);
    }
}
