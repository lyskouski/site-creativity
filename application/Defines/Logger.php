<?php namespace Defines;

/**
 * Define all Request Methods that IS USED in IAC
 * @see ListInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines
 */
class Logger implements ListInterface
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;

    public static function getDefault ()
    {
        return self::DEBUG;

    }

    public static function getList ()
    {
        return (new \System\Aggregator )->const2array( __CLASS__ );

    }

    public static function getName( $iLvl ) {
        $aValues = (new \System\Aggregator )->const2array( __CLASS__, true );
        return $aValues[ $iLvl ];
    }

}
