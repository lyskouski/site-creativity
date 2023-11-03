<?php namespace Modules\Log\Auth\Mail;

use Defines\Response\Code;
use Defines\User\Account;
use Engine\Request\Input\Server;
use Engine\Response\Template;

/**
 * Model object for Authentication via email
 *
 * @since 2015-09-30
 * @author Viachaslau Lyskouski
 * @package Modules/Log/Auth/Mail
 */
class Model extends \Modules\Log\Auth\ModelAbstract
{

    /**
     * Check email and password existance
     * @note \System\Registry::translation()->sys('LB_ERROR_INCORECT_EMAIL');
     * @note \System\Registry::translation()->sys('LB_ERROR_MISSING_PASSWORD');
     * @note \System\Registry::translation()->sys('LB_ERROR_SIMPLE_PASSWORD');
     *
     * @param string $sEmail
     * @param string $sPssw
     * @return boolean
     */
    protected function checkValues($sEmail, $sPssw)
    {
        $bResult = true;
        if (!$sPssw) {
            $this->updateResult('LB_ERROR_MISSING_PASSWORD', Code::E_FAILED, 'pssw');
            $bResult = false;
        } elseif (!$sEmail) {
            $this->updateResult('LB_ERROR_INCORECT_EMAIL', Code::E_FAILED, 'email');
            $bResult = false;
        }
        return $bResult;
    }

    /**
     * Get password from a crypted json
     *
     * @param string $sPssw
     * @param string $sPhrase
     * @return string
     */
    protected function getPassword($sPssw, $sPhrase = null)
    {
        $oCrypt = new \System\CryptoJS();
        return $oCrypt->decrypt(
                        $oCrypt->getPassphrase($sPhrase), json_decode($sPssw, true)
        );
    }

    /**
     * Get password from the database
     *
     * @param \Data\Doctrine\Main\UserAccount $oUser
     * @param string $sPssw
     * @return boolean
     */
    protected function validatePassword($oUser, $sPssw)
    {
        $sTime = $oUser->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP);
        $sValidate = $this->getPassword(
                json_encode(array(
            \System\CryptoJS::DATA => $oUser->getSecret(),
            \System\CryptoJS::SALT => $oUser->getExtra(),
            \System\CryptoJS::VECTOR => $oUser->getToken()
                )), $sPssw
        );
        return $sValidate === $sTime;
    }

    /**
     * Login be mail
     * @note \System\Registry::translation()->sys('LB_ERROR_USER_NOT_FOUND');
     * @note \System\Registry::translation()->sys('LB_ERROR_INCORRECT_PASSWORD');
     *
     * @param string $sEmail - has to be validated by FILTER_VALIDATE_EMAIL
     * @param string $sPssw - disable all possible filters
     * @return boolean
     */
    public function login($sEmail, $sPssw)
    {
        $oUser = null;
        if ($this->checkValues($sEmail, $sPssw)) {
            $oHelper = new \Data\UserHelper();
            $oHelper->getAttempt((new Server)->getUserIp());

            $oUser = $oHelper->findUser($sEmail, Account::MAIL);
            if (!$oUser) {
                $this->updateResult('LB_ERROR_USER_NOT_FOUND', Code::E_FAILED, 'email');
            // registry process was not finished
            } elseif ($oUser->getId() == \Defines\Database\Params::DEFAULT_USER) {
                return null;

            } elseif (!$this->validatePassword($oUser, $this->getPassword($sPssw))) {
                $this->updateResult('LB_ERROR_INCORRECT_PASSWORD', Code::E_FAILED, 'pssw');

            } else {
                \System\Registry::user()->setNewCookie($oUser->getUser());
                $this->updateResult();
            }
        }

        return $this->getCode() === Code::E_OK;
    }

    /**
     * Registry new user
     * @note \System\Registry::translation()->sys('LB_ERROR_EMAIL_EXIST');
     *
     * @param string $sEmail
     * @param string $sPssw
     * @return boolean
     */
    public function registry($sEmail, $sPssw)
    {
        if ($this->checkValues($sEmail, $sPssw)) {
            $oHelper = new \Data\UserHelper();

            $oUser = $oHelper->findUser($sEmail, Account::MAIL);
            if ($oUser) {
                $this->updateResult('LB_ERROR_EMAIL_EXIST', Code::E_FAILED, 'email');
            } else {
                // Update attempts to avoid spam-avalanche server down
                $oHelper->getAttempt((new Server)->getUserIp());
                // Decrypt the password
                $sPsswActual = $this->getPassword($sPssw);
                // Insert into database
                $oUserAccount = $oHelper->addUserAccount($sEmail, $sPsswActual);

                $sTitle = \System\Registry::translation()->sys('LB_MAIL_REGISTRY');
                $sMailContent = (new Template('Mail/Topic/registry'))
                        ->set('account', $sEmail)
                        ->set('type', Account::MAIL)
                        ->set('token', $oUserAccount->getToken())
                        ->compile();

                $this->sendMail2User($sEmail, $sTitle, $sMailContent);

                // Check user account existance, then update response
                $oUserAccount->getId() && $this->updateResult($sEmail);
            }
        }
        return $this->getCode() === Code::E_OK;
    }

    /**
     * Send a mail for restoring password for a user
     * @note \System\Registry::translation()->sys('LB_ERROR_INCORECT_EMAIL');
     * @note \System\Registry::translation()->sys('LB_ERROR_USER_NOT_FOUND');
     *
     * @param type $sEmail
     * @return type
     */
    public function restore($sEmail)
    {
        $oHelper = new \Data\UserHelper();
        // Update attempts to avoid spam-avalanche server down
        $oHelper->getAttempt((new Server)->getUserIp());

        if (!$sEmail) {
            $this->updateResult('LB_ERROR_INCORECT_EMAIL', Code::E_FAILED, 'email');
        } else {
            /* @var $oUser \Data\Doctrine\Main\UserAccount */
            $oUser = $oHelper->findUser($sEmail, Account::MAIL);
            if (!$oUser) {
                $this->updateResult('LB_ERROR_USER_NOT_FOUND', Code::E_FAILED, 'email');
            } else {
                $sTitle = \System\Registry::translation()->sys('LB_MAIL_RESTORE');
                $sMailContent = (new Template('Mail/Topic/restore'))
                        ->set('account', $sEmail)
                        ->set('type', Account::MAIL)
                        ->set('token', $this->getRestoreCode($oUser->getSecret()))
                        ->compile();
                $this->sendMail2User($sEmail, $sTitle, $sMailContent);
                $this->updateResult($sEmail);
            }
        }
        return $this->getCode() === Code::E_OK;
    }

    /**
     * Change password for a user
     * @note \System\Registry::translation()->sys('LB_ERROR_USER_NOT_FOUND');
     * @note \System\Registry::translation()->sys('LB_ERROR_DIFFERENT_PASSWORD');
     * @note \System\Registry::translation()->sys('LB_ERROR_TOKEN_MISMATCH');
     *
     * @param string $sEmail
     * @param string $sToken
     * @param string $sPssw
     * @param string $sPssw2
     * @return boolean
     */
    public function changePssw($sEmail, $sToken, $sPssw, $sPssw2)
    {
        if ($this->checkValues($sEmail, $sPssw)) {
            $oHelper = new \Data\UserHelper();
            /* @var $oUser \Data\Doctrine\Main\UserAccount */
            $oUser = $oHelper->findUser($sEmail, Account::MAIL);
            $sRealPssw = $this->getPassword($sPssw);
            if (!$oUser) {
                $this->updateResult('LB_ERROR_USER_NOT_FOUND', Code::E_FAILED, 'token');
            } elseif (!$sRealPssw || $sRealPssw !== $this->getPassword($sPssw2)) {
                $this->updateResult('LB_ERROR_DIFFERENT_PASSWORD', Code::E_FAILED, 'pssw_retry');
            } elseif ($this->getRestoreCode($oUser->getSecret()) !== $sToken) {
                $this->updateResult('LB_ERROR_TOKEN_MISMATCH', Code::E_FAILED, 'token');
            } else {
                $oHelper->addUserAccount($oUser->getAccount(), $sRealPssw, $oUser);
                $this->updateResult();
            }
        }

        return $this->getCode() === Code::E_OK;
    }

    /**
     * Generate resoring code for a mail
     *
     * @param string $sSalt
     * @return string
     */
    public function getRestoreCode($sSalt)
    {
        return hash('sha256', $sSalt . date(\Defines\Database\Params::DATE_FORMAT), false);
    }

    /**
     * Add mail to a cron submit
     *
     * @param string $mailTo
     * @param string $title
     * @param string $content
     * @return boolean
     */
    protected function sendMail2User($mailTo, $title, $content)
    {
        $contentHtml = (new Template('Mail/template'))
                ->set('title', $title)
                ->set('content', $content)
                ->compile();
        $sendMail = new \Engine\Response\Mail();
        $success = $sendMail->sendMail($mailTo, $title, $contentHtml);
        if (!$success) {
            throw new \Error\Validation($sendMail->getError());
        }
        return $success;
    }

}
