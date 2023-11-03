<?php namespace Defines;

/**
 * Define all Request Methods that is used in the project
 * @see ListInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines
 */
class Extension extends ListAbstract
{

    const HTML = 'html';
    const JSON = 'json';
    const XML = 'xml';
    const TXT = 'txt';
    const RSS = 'rss';
    const PNG = 'png';

    /**
     * @var string - Accelerated Mobile Pages (AMP)
     */
    const AMP = 'amp';

    public static function getDefault ()
    {
        return self::HTML;

    }

}
