<?php namespace Defines\Content;

/**
 * Define all content type
 *
 * @author Viachaslau Lyskouski
 * @since 2015-12-11
 * @package Defines
 */
class Attribute extends \Defines\ListAbstract
{

    const TYPE_IMG = 'og:image';
    const TYPE_TITLE = 'og:title';
    const TYPE_KEYS = 'keywords';
    const TYPE_DESC = 'description';
    const TYPE_AUTHOR = 'author';

    /**
     * Auditor rejection reply
     */
    const TYPE_REPLY = 'og:reply';

    public static function getDefault()
    {
        return self::TYPE_TITLE;
    }

    public static function getMandatory() {
        return array(
            self::TYPE_TITLE,
            self::TYPE_DESC,
            self::TYPE_KEYS,
            self::TYPE_REPLY,
            'content#0'
        );
    }

    public static function getBasicList() {
        return array(
            self::TYPE_IMG,
            self::TYPE_TITLE,
            self::TYPE_KEYS,
            self::TYPE_DESC,
            self::TYPE_AUTHOR,
            self::TYPE_REPLY
        );
    }
}
