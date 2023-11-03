<?php namespace Access;

/**
 * User interface for authorization and permission verification
 * @todo [!] multiple roles (access) can be assigned
 *
 * @link http://doctrine-orm.readthedocs.org/en/latest/cookbook/entities-in-session.html - for a serialization into database
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 */
class User
{

    /**
     * Root privileges `cr_main`.`access` [`id`=0]
     * @var int
     */
    const ADMIN = 0;

    /**
     * Disabled user privileges `cr_main`.`access` [`id`=7]
     * @var int
     */
    const BLOCKED = 7;

    /**
     * Cookie value identificator
     * @var string
     */
    const COOKIE_AUTH = 'token';

    /**
     * Cookie value identificator
     * @var string
     */
    const DELIMITER = '|';

    /**
     * @var \Data\Doctrine\Main\Access
     */
    protected $oAccess;

    /**
     * @var array<\Data\Doctrine\Main\UserAccess>
     */
    protected $aAccountList;

    /**
     * @var array - list of Access IDs
     */
    protected $accessIds = array();

    /**
     * @var \Data\Doctrine\Main\User
     */
    protected $oUser;

    /**
     * @var \Data\UserHelper
     */
    protected $oHelper;

    /**
     * @todo accumulate all profiles
     *
     * @param string $sToken - token from cookies
     */
    public function __construct($sToken = null)
    {
        $oHelper = new \Data\UserHelper();
        $this->oHelper = $oHelper;

        $a = explode(self::DELIMITER, $sToken);
        if (sizeof($a) === 2) {
            $aAccountList = $oHelper->findUserByCookie($a[0], $a[1]);
        } else {
            $aAccountList = $oHelper->findUserByCookie(null);
        }
        $this->aAccountList = $aAccountList;

        // Database error
        if (!$aAccountList) {
            throw new \Error\Validation('Missing profile permissions', \Defines\Response\Code::E_FORBIDDEN);
        }

        /* @var $oActual \Data\Doctrine\Main\UserAccess */
        $oActual = current($aAccountList);
        $this->oAccess = $oActual->getAccess();
        $this->oUser = $oActual->getUser();
        foreach ($aAccountList as $oActual) {
            $this->accessIds[] = $oActual->getAccess()->getId();
            $this->accessIds = $oHelper->getUserProfiles($oActual->getAccess(), $this->accessIds);
        }

        // Cron is the same as root
        if (\System\Registry::cron()) {
            $this->oAccess = $oHelper->findProfile(self::ADMIN);
            $this->accessIds = array(self::ADMIN);
        }

        $this->accessIds = array_values(array_unique($this->accessIds));
    }

    /**
     * Get user object
     * @return \Data\Doctrine\Main\User
     */
    public function getEntity()
    {
        return $this->oUser;
    }

    /**
     * Get all user's accounts
     * @return array<\Data\Doctrine\Main\UserAccount>
     */
    public function getAccounts()
    {
        return $this->oHelper->getUserAccounts($this->oUser);
    }

    /**
     * Update cookie
     *
     * @param \Data\Doctrine\Main\User $oUser
     * @return integer - user id
     */
    public function setNewCookie(\Data\Doctrine\Main\User $oUser)
    {
        $sCookie = hash('sha512', $oUser->getUsername() . date(\Defines\Database\Params::TIMESTAMP), false);
        $oUser->setCookie($sCookie);

        $oManager = $this->oHelper->getEntityManager();
        $oManager->persist($oUser);
        $oManager->flush();
        // Update cookies
        (new \Engine\Request\Input)->setCookie(
                self::COOKIE_AUTH, $oUser->getId() . self::DELIMITER . $sCookie, \Defines\Database\Params::COOKIE_DAYS
        );

        return $oUser->getId();
    }

    /**
     * Logout - unbind cookie value in the database
     */
    public function out()
    {
        // Clear cookies in the browser
        (new \Engine\Request\Input)->delCookie(self::COOKIE_AUTH);

        // Clear cookies in the database
        $user = $this->oUser;
        $user->setCookie(null);
        $oManager = $this->oHelper->getEntityManager();
        $oManager->persist($user);
        $oManager->flush();
    }

    /**
     * Check if user was authorized
     * @return boolean
     */
    public function isLogged()
    {
        return $this->oUser->getId() !== \Defines\Database\Params::DEFAULT_USER;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getName()
    {
        return $this->oUser->getUsername();
    }

    /**
     * The main role is admin
     * @note string comparison is needed for a corret SQLite behaviour testing
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return \System\Registry::cron() || $this->isLogged() && (string) $this->oAccess->getId() === (string) self::ADMIN;
    }

    /**
     * Get permission object
     * @todo - check a custom permissions
     *
     * @param string $sModuleUrl
     * @param string $sAction
     * @return boolean
     */
    public function checkAccess($sModuleUrl, $sAction = 'index')
    {
        // There are missing any obstacles for admin
        if ($this->isAdmin()) {
            return true;
        }

        // Check permission
        $oAccessUrl = new \Data\UserHelper();
        $aPermit = $oAccessUrl->findUrl($sModuleUrl, $sAction);

        $bResult = false;
        if (
                !array_intersect($this->accessIds, $aPermit[false])
                && array_intersect($this->accessIds, $aPermit[true])
        ) {
            $bResult = true;
        }

        return $bResult;
    }

}
