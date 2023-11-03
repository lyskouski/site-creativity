<?php namespace Defines\Database;

/**
 * Description of Board List
 *
 * @author s.lyskovski
 */
class BoardCategory extends \Defines\ListAbstract
{

    const RECENT = '000';
    const ACTIVE = '111';
    const ALPHA = '700';
    const BETTA = '770';
    const FINISH = '777';
    const DELETE = '999';

    public static function getDefault()
    {
        return self::RECENT;
    }

    public static function getName($key)
    {
        $index = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $oTranslate = \System\Registry::translation();
        $list = array(
            self::RECENT => $oTranslate->sys('LB_TODO_DO'),
            self::ACTIVE => $oTranslate->sys('LB_TODO_IN'),
            self::BETTA => '[&beta;] ' . $oTranslate->sys('LB_TODO_OK'),
            self::ALPHA => '[&alpha;] ' . $oTranslate->sys('LB_TODO_OK'),
            self::FINISH => $oTranslate->sys('LB_TODO_OK'),
            self::DELETE => $oTranslate->sys('LB_TODO_NO')
        );

        return $list[$index];
    }

    public static function getIcon($key, $value, $tag = true)
    {
        $index = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $list = array(
            self::RECENT => '&lowast; '.($tag ? '<span class="co_attention">%s</span>' : '%s'),
            self::ACTIVE => '&orarr; '.($tag ? '<strong>%s</strong>' : '%s'),
            self::BETTA => '&beta; '.($tag ? '<em>%s</em>' : '%s'),
            self::ALPHA => '&alpha; '.($tag ? '<em>%s</em>' : '%s'),
            self::FINISH => '&radic; '.($tag ? '<em>%s</em>' : '%s'),
            self::DELETE => '&times; '.($tag ? '<s>%s</s>' : '%s'),
        );
        return sprintf($list[$index], $value);
    }
}
