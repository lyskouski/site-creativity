<?php namespace Defines;

/**
 * Define all Request Methods that IS USED in IAC
 * @see ListInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines
 */
class RequestMethod implements ListInterface
{

    /**
     * GET requests identificator
     * @var string
     */
    const GET = 'GET';

    /**
     * POST requests identificator
     * @var string
     */
    const POST = 'POST';

    /**
     * HEAD requests identificator
     * @var string
     */
    const HEAD = 'HEAD';

    /**
     * DELETE requests identificator
     * @var string
     */
    const DELETE = 'DELETE';

    /**
     * PUT requests identificator
     * @var string
     */
    const PUT = 'PUT';

    public static function getDefault ()
    {
        return self::GET;

    }

    public static function getList ()
    {
        return (new \System\Aggregator )->const2array( __CLASS__ );

    }

}
