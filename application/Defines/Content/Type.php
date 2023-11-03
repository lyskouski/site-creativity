<?php namespace Defines\Content;
/**
 * Define all Publication's types
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines
 */
class Type extends \Defines\ListAbstract
{
    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_PROSE')
     * \System\Registry::translation()->sys('LB_OEUVRE_PROSE_DESC')
     * @var string - prose identificator
     */
    const PROSE = 'prose';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_POETRY')
     * \System\Registry::translation()->sys('LB_OEUVRE_POETRY_DESC')
     * @var string - poetry identificator
     */
    const POETRY = 'poetry';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_DRAWING')
     * \System\Registry::translation()->sys('LB_OEUVRE_DRAWING_DESC')
     * @var string - drawing identificator
     */
    const DRAWING = 'drawing';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_MUSIC')
     * \System\Registry::translation()->sys('LB_OEUVRE_MUSIC_DESC')
     * @var string - music identificator
     */
    const MUSIC = 'music';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_ARTICLE')
     * \System\Registry::translation()->sys('LB_OEUVRE_ARTICLE_DESC')
     * @var string - article identificator
     */
    const ARTICLE = 'article';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_BOOK')
     * \System\Registry::translation()->sys('LB_OEUVRE_BOOK_DESC')
     * @var string - book identificator
     */
    const BOOK = 'book';

    /**
     * \System\Registry::translation()->sys('LB_OEUVRE_BOOK_SERIES')
     * \System\Registry::translation()->sys('LB_OEUVRE_BOOK_SERIES_DESC')
     * @var string - book identificator
     */
    const BOOK_SERIES = 'book/series';

    public static function getDefault()
    {
        return self::PROSE;
    }

    public static function getPublications()
    {
        return [
            'oeuvre/poetry/i%',
            'oeuvre/prose/i%',
            'mind/article/i%'
        ];
    }
}
