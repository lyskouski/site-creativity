<?php namespace Defines\Database;

/**
 * Branches
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Defines/Database
 */
class Branch
{

    /**
     * @var integer - alpha-version of the site
     */
    const ALPHA = 0;

    /**
     * @var integer - beta-version of the site
     */
    const BETA = 1;

    /**
     * @var integer - actual (public) version of the site
     */
    const LIVE = 9;

}
