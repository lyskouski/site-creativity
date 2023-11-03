<?php namespace Defines\User;

/**
 * Table `cr_main`.`access` attribute
 * X - author / auditor / moder
 * X - group
 * X - others
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Database
 */
class Access
{

    /**
     * Only root access
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_OWNER')
     */
    const OWNER = 0;

    /**
     * The content for audit purposes
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_AUDIT')
     */
    const AUDIT = 1;

    /**
     * Moderation is required
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_MODERATE')
     */
    const MODERATE = 2;

    /**
     * Access for reading (authorised users)
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_READ')
     */
    const READ = 3;

    /**
     * Can be marked
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_MARK')
     */
    const MARK = 4;

    /**
     * Can be commented
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_COMMENT')
     */
    const COMMENT = 5;

    /**
     * Approved for transation purposes
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_TRANSLATE')
     */
    const TRANSLATE = 6;

    /**
     * Can be edited
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_EDIT')
     */
    const EDIT = 7;

    /**
     * Can be deleted
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_DELETE')
     */
    const DELETE = 8;

    /**
     * Disabled option
     * @note \System\Registry::translation()->sys('LB_USER_ACCESS_BLOCK')
     */
    const BLOCK = 9;

    /**
     * Get audit combined value
     *
     * @return string
     */
    public static function getAudit()
    {
        return self::AUDIT . self::AUDIT . self::OWNER;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getModerate()
    {
        return self::READ . self::READ . self::MODERATE;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getModApprove()
    {
        return self::COMMENT . self::COMMENT . self::READ;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getModDecline()
    {
        return self::BLOCK . self::BLOCK . self::BLOCK;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getModClosed()
    {
        return self::READ . self::READ . self::READ;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getNew()
    {
        return self::DELETE . self::OWNER . self::OWNER;
    }

        /**
     * Get new combined value
     *
     * @return string
     */
    public static function getNewTopic()
    {
        return self::COMMENT . self::COMMENT . self::READ;
    }

    /**
     * Get new combined value
     *
     * @return string
     */
    public static function getAccessNew()
    {
        return self::EDIT . self::OWNER . self::OWNER;
    }

    /**
     * Get list of access rules with description
     *
     * @return array - [[code => title],..]
     */
    public static function getList ()
    {
        $aList = (new \System\Aggregator )->const2array( __CLASS__, true );
        $oTranslate = \System\Registry::translation();
        foreach ($aList as &$sTitle) {
            $sTitle = $oTranslate->sys("LB_USER_ACCESS_{$sTitle}");
        }
        return $aList;

    }
}
