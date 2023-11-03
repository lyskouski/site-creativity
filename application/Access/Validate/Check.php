<?php namespace Access\Validate;

use Data\Doctrine\Main\Content;
use Defines\User\Access;

/**
 * Validate access to comment, reply and vote for comments
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Access
 */
class Check
{

    const USER = 0;
    const GROUP = 1;
    const OTHER = 2;

    protected $type;

    /**
     * Set type of a validation
     * @see \Defines\User\Access
     *
     * @return integer - \Defines\User\Access::{const}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type of validation
     *
     * @param integer $type - \Defines\User\Access::{const}
     * @return \Access\Validate\Check
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Check access to reply
     * @param \Data\Doctrine\Main\Content $oContent
     * @return boolean
     */
    public function isAccepted(Content $oContent = null)
    {
        if (is_null($oContent)) {
            $oContent = \System\Registry::stat()->getContent();
        }
        if (!$oContent) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_204'),
                \Defines\Response\Code::E_NO_CONTENT
            );
        }
        $sAccess = $oContent->getAccess();
        $user = \System\Registry::user();

        $bUser = $oContent->getAuthor() && $oContent->getAuthor()->getUsername() === $user->getName();
        $logged = \System\Registry::user()->isLogged();

        $bAllow = false;
        if (
                $sAccess[self::USER] == Access::BLOCK && $bUser
                || $sAccess[self::GROUP] == Access::BLOCK && $logged
                || $sAccess[self::OTHER] == Access::BLOCK && !$logged
        ) {
            // $bAllow = \System\Registry::user()->isAdmin();

        // Basic ability
        } elseif (
                $sAccess[self::USER] >= $this->getType() && $bUser
                || $sAccess[self::GROUP] >= $this->getType() && $logged
                || $sAccess[self::OTHER] >= $this->getType() && !$logged
        ) {
            $bAllow = true;

        // Check special privilegues
        } elseif (
            $this->getType() === Access::READ && $sAccess[self::USER] < $this->getType() && $bUser
            || $this->getType() === Access::READ && $sAccess[self::GROUP] === Access::MODERATE  && $user->checkAccess('dev/tasks/moder', 'index')
            || $this->getType() === Access::READ && $sAccess[self::GROUP] === Access::AUDIT  && $user->checkAccess('dev/tasks/auditor', 'index')
            || $this->getType() === Access::READ && $sAccess[self::GROUP] === Access::OWNER  && $user->isAdmin()
        ) {
            $bAllow = true;
        }
        return $bAllow;
    }

}
