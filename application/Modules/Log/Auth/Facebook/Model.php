<?php namespace Modules\Log\Auth\Facebook;

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
     * Facebook Authentication
     *
     * @return string
     * @throws \Error\Validation
     */
    protected function getToken($access)
    {
        $api = \System\Registry::config()->getSocialApi('facebook');
        $fb = new \Facebook\Facebook(array(
            'app_id' => $api['id'],
            'app_secret' => $api['secret'],
            'default_graph_version' => 'v2.5',
        ));

        try {
            $fb->setDefaultAccessToken($access);
            $helper = $fb->getJavaScriptHelper();
            $accessToken = $helper->getAccessToken();
        // When Graph returns an error
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            throw new \Error\Validation('Graph error: ' . $e->getMessage());
        // When validation fails or other local issues
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            throw new \Error\Validation('Facebook SDK error: ' . $e->getMessage());
        }

        if (!isset($accessToken)) {
            throw new \Error\Validation('No cookie set or no OAuth data could be obtained from cookie.');
        }
        return $accessToken;
    }

    /**
     * Login by facebook
     *
     * @param array $sEmail - has to be validated by FILTER_VALIDATE_EMAIL
     * @param string $sPssw - disable all possible filters
     * @return boolean
     */
    public function login($aParams)
    {
        $secret = $this->getToken($aParams['accessToken']);
        $account = \System\Registry::user()->getEntity();
        $dataHelper = new \Data\UserHelper();

        $result = true;
        $user = $dataHelper->findUser($aParams['userID'], Account::FACEBOOK);
        if (!$user) {
            $user = new \Data\Doctrine\Main\UserAccount();
            $user->setAccount($aParams['userID'])
                ->setType(Account::FACEBOOK)
                ->setUser($account);
            $result = null;
        }

        $user->setSecret($secret)
        //        ->setExtra($aParams['signed_request'])
                ->setToken($aParams['accessToken'])
                ->setUpdatedAt(new \DateTime);

        $em = \System\Registry::connection();
        $em->persist($user);
        $em->flush();

        if (!\System\Registry::user()->isLogged()) {
            $result = null;
            if ($user->getUser() !== $account) {
                \System\Registry::user()->setNewCookie($user->getUser());
                $result = true;
            }
        }

        $this->updateResult($aParams['userID']);

        return $result;

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

}
