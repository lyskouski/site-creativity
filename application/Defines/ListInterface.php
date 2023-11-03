<?php namespace Defines;

/**
 * Common defines functionality
 * @note http://php.net/manual/en/migration52.incompatible.php - 'abstract static function'
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Defines
 */
interface ListInterface
{
    /**
     * Get list of constants
     * * do not change this list, it has to return ALL constants
     * * do not add to a class other constants, they has to be related ONLY for the current type
     *
     * @note if it's needed a limited list, then create function get{.*?}List()
     *
     * @return array
     */
    public static function getList ();

    /**
     * Get a default value
     *
     * @return mixed
     */
    public static function getDefault ();

}
