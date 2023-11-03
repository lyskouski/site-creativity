<?php namespace Defines;

/**
 * Type of server modes
 * @see ListInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Defines
 */
class ServerType extends \Defines\ListAbstract
{

    /**
     * Production environment
     * @var string
     */
    const LIVE = 'live';

    /**
     * Testing environment
     * @var string
     */
    const TEST = 'test';

    /**
     * Internal environment (AT WORK)
     * @var string
     */
    const DEV = 'development_itos';

    /**
     * Internal environment (HOME)
     * @var string
     */
    const LOCAL = 'development_home';

    /**
     * Branch for initial testing
     * @var string
     */
    const ALPHA = 'alpha';

    public static function getDefault()
    {
        return self::LIVE;
    }

}
