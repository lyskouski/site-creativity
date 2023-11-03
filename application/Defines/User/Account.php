<?php

namespace Defines\User;

/**
 * Table `cr_main`.`user_account`:`type`-field]
 * @note var string - is needed for a validation via GET
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Database
 */
class Account extends \Defines\ListAbstract
{

    /**
     * Authentication by mail
     * @note \System\Registry::translation()->sys('LB_AUTH_MAIL')
     * @note \System\Registry::translation()->sys('LB_AUTH_MAIL_DESCRIPTION')
     */
    const MAIL = '1';

    /**
     * Authentication by vk.com
     * @note \System\Registry::translation()->sys('LB_AUTH_VK')
     * @note \System\Registry::translation()->sys('LB_AUTH_VK_DESCRIPTION')
     */
    const VK = '4';

    /**
     * Authentication by Person
     * @note \System\Registry::translation()->sys('LB_AUTH_PERSONA')
     * @note \System\Registry::translation()->sys('LB_AUTH_PERSONA_DESCRIPTION')
     */
    const PERSONA = '2';

    /**
     * Authentication by Facebook
     * @note \System\Registry::translation()->sys('LB_AUTH_FACEBOOK')
     * @note \System\Registry::translation()->sys('LB_AUTH_FACEBOOK_DESCRIPTION')
     */
    const FACEBOOK = '3';

    /**
     * Authentication by Twitter
     * @note \System\Registry::translation()->sys('LB_AUTH_TWITTER')
     * @note \System\Registry::translation()->sys('LB_AUTH_TWITTER_DESCRIPTION')
     */
    const TWITTER = '5';

    /**
     * Authentication by Google
     * @note \System\Registry::translation()->sys('LB_AUTH_GOOGLE')
     * @note \System\Registry::translation()->sys('LB_AUTH_GOOGLE_DESCRIPTION')
     */
    const GOOGLE = '6';

    /**
     * Authentication by Linkedin
     * @note \System\Registry::translation()->sys('LB_AUTH_LINKEDIN')
     * @note \System\Registry::translation()->sys('LB_AUTH_LINKEDIN_DESCRIPTION')
     */
    const LINKEDIN = '8';

    /**
     * Authentication by ICQ
     * @note \System\Registry::translation()->sys('LB_AUTH_ICQ')
     * @note \System\Registry::translation()->sys('LB_AUTH_ICQ_DESCRIPTION')
     */
    const ICQ = '7';

    /**
     * Authentication by Viber
     * @note \System\Registry::translation()->sys('LB_AUTH_VIBER')
     * @note \System\Registry::translation()->sys('LB_AUTH_VIBER_DESCRIPTION')
     */
    const VIBER = '9';

    /**
     * Authentication by Telegram
     * @note \System\Registry::translation()->sys('LB_AUTH_TELEGRAM')
     * @note \System\Registry::translation()->sys('LB_AUTH_TELEGRAM_DESCRIPTION')
     */
    const TELEGRAM = '10';

    /**
     * Authentication by WhatsUp
     * @note \System\Registry::translation()->sys('LB_AUTH_WHATSUP')
     * @note \System\Registry::translation()->sys('LB_AUTH_WHATSUP_DESCRIPTION')
     */
    const WHATSUP = '11';


    public static function getDefault()
    {
        return self::MAIL;
    }

    /**
     * Return named identificators for AUTH
     *
     * @return array
     */
    public static function getTextList( $bLower = false)
    {
        $aList = (new \System\Aggregator)->const2array(__CLASS__, true);
        if ($bLower) {
            $aList = array_flip( array_change_key_case(array_flip($aList), CASE_LOWER) );
        }
        return $aList;
    }

    /**
     * Get colors identificator for Authentication type
     *
     * @param string $sId
     * @return array
     */
    public static function getColor($sId)
    {
        $aColors = array('707070', '000000');
        switch ($sId) {
            case self::TELEGRAM:
                $aColors = array('0091d4', '019cdcf');
                break;
            case self::VIBER:
                $aColors = array('9a48cd', '704891');
                break;
            case self::LINKEDIN:
                $aColors = array('0b2b5c', '0b2b5c');
                break;
            case self::ICQ:
                $aColors = array('44aa00', '44aa00');
                break;
            case self::GOOGLE:
                $aColors = array('DC4A38', 'DC4A38');
                break;
            case self::TWITTER:
                $aColors = array('2aa9e0', '2aa9e0');
                break;
            case self::VK:
                $aColors = array('4c75a3', '4c75a3');
                break;
            case self::FACEBOOK:
                $aColors = array('3C5A99', '3C5A99');
                break;
            case self::PERSONA:
                $aColors = array('ca4e24', 'ca4e24');
                break;
            case self::WHATSUP:
                $aColors = array('44aa00', '44aa00');
                break;
        }
        return $aColors;
    }

}
