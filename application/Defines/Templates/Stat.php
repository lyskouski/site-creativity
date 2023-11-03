<?php namespace Defines\Templates;

/**
 * Helper for statistics
 *
 * @author Viachaslau Lyskouski
 * @since 2015-10-19
 * @package Defines/Templates
 */
class Stat extends \Defines\ListAbstract
{

    /**
     * Text element
     * @see application/Views/Stat/text.php
     * @var string
     */
    const TEXT = 'Stat/text';

    /**
     * Image element
     * @see application/Views/Stat/image.php
     * @var string
     */
    const IMAGE = 'Stat/image';

    /**
     * List of publications
     * @see application/Views/Stat/publications.php
     * @var string
     */
    const PUBLICATIONS = 'Stat/publications';

    /**
     * Horisontal separator
     * @see application/Views/Stat/grid.php
     * @var string
     */
    const GRID = 'Stat/grid';

    /**
     * Vertical separator
     * @see application/Views/Stat/list.php
     * @var string
     */
    const SKED = 'Stat/sked';

    public static function getDefault()
    {
        return self::TEXT;
    }
}
