<?php namespace Data;

use Defines\Database\CrMain;
use Data\Doctrine\Main\Access;
use Defines\User\Account;

/**
 * User management helper
 *
 * @author Viachaslau Lyskouski
 * @since 2015-09-21
 * @package Data
 */
class UserHelper extends HelperAbstract
{

    protected function getTarget()
    {
        return CrMain::ACCESS_ACTION;
    }

    /**
     * Get unautorized user for a data manipulation
     *
     * @return \Data\Doctrine\Main\User
     */
    public function getUndefinedUser()
    {
        return $this->getEntityManager()->find(CrMain::USER, \Defines\Database\Params::DEFAULT_USER);
    }

    /**
     * Get username if defined
     * 
     * @param \Data\Doctrine\Main\User $obj
     * @return string
     */
    public static function getUsername(\Data\Doctrine\Main\User $obj = null)
    {
        $username = 'Anonimus';
        if ($obj) {
            $username = $obj->getUsername();
        }
        return $username;
    }

    /**
     * Get unautorized user for a data manipulation
     *
     * @param string $sName
     * @param boolean $bDirect
     * @return \Data\Doctrine\Main\User
     */
    public function getUserByName($sName, $bDirect = true)
    {
        /* @var $rep \Doctrine\ORM\EntityRepository */
        $rep = $this->getEntityManager()->getRepository(CrMain::USER);
        if ($bDirect) {
            $user = $rep->findOneBy(array(
                'username' => $sName
            ));
        } else {
            $user = $rep->createQueryBuilder('u')
                ->select()
                ->where('u.username LIKE :username')
                ->setParameter('username', str_replace([' ', '-'], ['_', '_'], $sName))
                ->getQuery()->getSingleResult();
        }

        return $user;
    }

    /**
     * Get user attempts to check authentication restriction
     *
     * @param string $sAddress - IP address
     * @return integer
     */
    public function getAttempt($sAddress)
    {
        $oManager = $this->getEntityManager();
        $oUndefUser = $this->getUndefinedUser();
        /* @var $oProtocol \Data\Doctrine\Main\UserProtocol */
        $oProtocol = $oManager->getRepository(CrMain::USER_PROTOCOL)->findOneBy(array(
            'user' => $oUndefUser,
            'address' => $sAddress
        ));
        // Check limitation
        if ($oProtocol && \Defines\Database\Params::USER_PROTOCOL_LIMIT == $oProtocol->getAttemps()) {
            $oDate = new \DateTime("-1 hour");
            // @fixme: restore functionality
            //if ($oProtocol->getUpdatedAt() > $oDate ) {
            //    throw new \Error\Validation(\System\Registry::translation()->sys('LB_ERROR_AUTH_LIMITATION'));
            //}
            $oProtocol->setAttemps(0);
            // Increase value
        } elseif ($oProtocol) {
            $oProtocol->setAttemps(1 + $oProtocol->getAttemps());
            // Create if missing
        } else {
            $oProtocol = new \Data\Doctrine\Main\UserProtocol();
            $oProtocol->setAddress($sAddress);
            $oProtocol->setUpdatedAt(new \DateTime());
            $oProtocol->setUser($oUndefUser);
        }
        $oManager->persist($oProtocol);
        $oManager->flush();
        return $oProtocol->getAttemps();
    }

    /**
     * Add into database a new account [OR] update current
     *
     * @param string $sEmail - email adress
     * @param string $sPssw - real (undecoded) value
     * @param \Data\Doctrine\Main\UserAccount $oUserAccount
     * @return \Data\Doctrine\Main\UserAccount
     */
    public function addUserAccount($sEmail, $sPssw, $oUserAccount = null)
    {
        if (is_null($oUserAccount)) {
            $oUserAccount = new \Data\Doctrine\Main\UserAccount();
            $oUserAccount->setType(Account::MAIL)
                ->setUser($this->getUndefinedUser())
                ->setAccount($sEmail);
        }

        $oDate = new \DateTime();
        $aCryptData = (new \System\CryptoJS)->encrypt(
            $sPssw, $oDate->format(\Defines\Database\Params::TIMESTAMP)
        );

        $oUserAccount->setUpdatedAt($oDate)
            ->setSecret($aCryptData[\System\CryptoJS::DATA])
            ->setExtra($aCryptData[\System\CryptoJS::SALT])
            ->setToken($aCryptData[\System\CryptoJS::VECTOR]);

        $this->getEntityManager()->persist($oUserAccount);
        $this->getEntityManager()->flush();
        return $oUserAccount;
    }

    /**
     * Find user to check authentication
     *
     * @param string $sAccount
     * @param integer $iType - constant from \Defines\User\Account
     * @return \Data\Doctrine\Main\UserAccount
     */
    public function findUser($sAccount, $iType)
    {
        return $this->getEntityManager()->getRepository(CrMain::USER_ACCOUNT)->findOneBy(array(
                'account' => $sAccount,
                'type' => $iType
        ));
    }

    /**
     * Find user by cookie
     *
     * @param integer $id
     * @param string $sCookie
     * @return array<\Data\Doctrine\Main\UserAccess>
     */
    public function findUserByCookie($id, $sCookie = '')
    {
        $oUser = null;
        if ($id && $sCookie) {
            $oUser = $this->getEntityManager()->getRepository(CrMain::USER)->find((int) $id);
            if (!$oUser || !$oUser->getCookie() || $sCookie !== $oUser->getCookie()) {
                (new \Engine\Request\Input)->delCookie(\Access\User::COOKIE_AUTH);
                $oUser = null;
            }
        }
        if (!$oUser) {
            $oUser = $this->getUndefinedUser();
        }
        return $this->getUserPrivileges($oUser);
    }

    /**
     * Get user's access profile
     *
     * @param int $id
     * @return \Data\Doctrine\Main\Access
     */
    public function findProfile($id)
    {
        return $this->getEntityManager()->getRepository(CrMain::ACCESS)->find($id);
    }

    /**
     * Get all possible profiles
     *
     * @return \Data\Doctrine\Main\Access
     */
    public function getAllProfiles()
    {
        return $this->getEntityManager()->getRepository(CrMain::ACCESS)->findAll();
    }

    /**
     * Get all related profiles
     *
     * @param \Data\Doctrine\Main\Access $oAccess
     * @return array<\Data\Doctrine\Main\AccessAction>
     */
    public function getRelatedActions(Access $oAccess)
    {
        return $this->getEntityManager()->getRepository(CrMain::ACCESS_ACTION)->findBy(array(
                'access' => $oAccess
        ));
    }

    /**
     * Get all possible profiles
     *
     * @return array<\Data\Doctrine\Main\Action>
     */
    public function getAllActions()
    {
        return $this->getEntityManager()->getRepository(CrMain::ACTION)->findBy(array(), array('url' => 'ASC'));
    }

    /**
     * Get all possible profiles
     *
     * @param int $id
     * @return \Data\Doctrine\Main\Action
     */
    public function findAction($id)
    {
        return $this->getEntityManager()->getRepository(CrMain::ACTION)->find($id);
    }

    /**
     * Prepare permission list
     *
     * @param \Data\Doctrine\Main\Access $oAccess
     * @param array $aResult
     * @return array
     */
    public function getUserProfiles(Access $oAccess, array &$aResult = array())
    {
        $aResult[] = $oAccess->getId();
        if ($oAccess->getAccess()) {
            $this->getUserProfiles($oAccess->getAccess(), $aResult);
        }
        return $aResult;
    }

    /**
     * Get user privileges list
     *
     * @param \Data\Doctrine\Main\User $oUser
     * @return array<\Data\Doctrine\Main\UserAccess>
     */
    public function getUserPrivileges(\Data\Doctrine\Main\User $oUser)
    {
        return $this->getEntityManager()->getRepository(CrMain::USER_ACCESS)->findBy(array(
                'user' => $oUser
        ));
    }

    /**
     * Get all possible profiles
     *
     * @return array<\Data\Doctrine\Main\UserAccount>
     */
    public function getUserAccounts(\Data\Doctrine\Main\User $oUser)
    {
        return $this->getEntityManager()->getRepository(CrMain::USER_ACCOUNT)->findBy(array(
                'user' => $oUser
        ));
    }

    /**
     * Get account types for a target url+action
     * @sample ->findUrl('index', 'indexAction');
     *
     * @param string $sUrl
     * @param string $sAction
     * @return
     */
    public function findUrl($sUrl, $sAction)
    {
        $aResult = array(
            true => array(),
            false => array()
        );
        /* @var $oAction \Data\Doctrine\Main\Action */
        $oAction = $this->getEntityManager()->getRepository(CrMain::ACTION)->findOneBy(array(
            'url' => $sUrl,
            'action' => str_replace('Action', '', $sAction)
        ));
        $aDbValues = $this->getEntityManager()->getRepository(CrMain::ACCESS_ACTION)->findBy(array(
            'action' => $oAction
        ));
        /* @var $oAccessUrl \Data\Doctrine\Main\AccessAction */
        foreach ($aDbValues as $oAccessUrl) {
            $aResult[$oAccessUrl->getPermission()][] = $oAccessUrl->getAccess()->getId();
        }
        return $aResult;
    }
}
