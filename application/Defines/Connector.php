<?php namespace Defines;

/**
 * Define all Database connection types
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines
 */
class Connector
{
    /**
     * Native PDO MySQL connection
     * @see \System\Database\Connector::__construct
     */
    const MYSQL = 'mysql';

    /**
     * Doctrine 2 ORM
     * @see \System\Database\Connector::__construct
     */
    const MYSQL_DOCTRINE = 'doctrine';

}
