<?php

namespace Modules\Log\Auth;

use Error\TextAbstract;
use System\ArrayUndef;
use Defines\Database\CrMain;

/**
 * Link account to a user profile
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log/Auth
 */
class Model
{

    protected $aError = array(
        TextAbstract::E_MESSAGE => '',
        TextAbstract::E_CODE => \Defines\Response\Code::E_OK
    );

    /**
     *
     * @param type $aData
     */
    public function linkProfile( $aData )
    {
        $oTranslation = \System\Registry::translation();
        if (!isset($aData['type']) || !isset($aData['account'])) {
            throw new \Error\Validation(
                $oTranslation->sys( 'LB_ERROR_MISSING_DATA' ),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }

        $sUsername = ArrayUndef::getValue($aData, 'username', '', FILTER_SANITIZE_STRING);
        $sToken = ArrayUndef::getValue($aData, 'token', '', FILTER_SANITIZE_STRING);

        if (!(preg_match("/^[a-zA-Z0-9_]{3,24}$/sui", $sUsername) || preg_match("/^[\\p{Cyrillic}0-9_]{3,24}$/sui", $sUsername))) {
            return $this->setError( $oTranslation->sys( 'LB_ERROR_USERNAME_INVALID' ) );
        }

        $oUserHelper = new \Data\UserHelper();
        if ($oUserHelper->getUserByName($sUsername)) {
            return $this->setError( $oTranslation->sys( 'LB_ERROR_USERNAME_EXISTS' ) );
        }

        // Check for mail a token value
        $oAccount = $oUserHelper->findUser($aData['account'], $aData['type']);
        if (!$oAccount) {
            return $this->setError($oTranslation->sys('LB_ERROR_USER_NOT_FOUND'));

        } elseif ($aData['type'] === \Defines\User\Account::MAIL && $oAccount->getToken() !== $sToken) {
            return $this->setError($oTranslation->sys('LB_ERROR_TOKEN_MISMATCH'));
        }

        if (\System\Registry::user()->isLogged()) {
            $oUser = \System\Registry::user()->getEntity();
        } else {
            $oUser = new \Data\Doctrine\Main\User();
            $oUser->setUsername($sUsername);
        }
        $id = \System\Registry::user()->setNewCookie( $oUser );
        // Validate that user has been added
        if (!$id) {
            throw new \Error\Validation(
                $oTranslation->sys( 'LB_ERROR_USER_NOT_FOUND' ),
                \Defines\Response\Code::E_NOT_FOUND
            );
        }
        $em = $oUserHelper->getEntityManager();
        // Update account
        $oAccount->setUser( $oUser );
        $em->persist($oAccount);
        // Add user role
        $oUserAccess = new \Data\Doctrine\Main\UserAccess();
        $oUserAccess->setUser( $oUser );
        $oUserAccess->setAccess(
            $em->getRepository(CrMain::ACCESS)->findOneByTitle('LB_ACCESS_AUTHOR')
        );

        $em->persist( $oUserAccess );
        $em->flush();
        return true;
    }

    /**
     *
     * @param string $sMessage
     * @return boolean - false
     */
    protected function setError($sMessage)
    {
        $this->aError = array(
            TextAbstract::E_MESSAGE => $sMessage,
            TextAbstract::E_CODE => \Defines\Response\Code::E_BAD_REQUEST
        );
        return false;
    }

    public function getError()
    {
        return $this->aError;
    }

}
