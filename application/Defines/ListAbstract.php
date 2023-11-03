<?php namespace Defines;

/**
 * Description of ListAbstract
 *
 * @author slaw
 */
abstract class ListAbstract implements ListInterface
{

    public static function getList()
    {
        return (new \System\Aggregator)->const2array(static::class);
    }

}
