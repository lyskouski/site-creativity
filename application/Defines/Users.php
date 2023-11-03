<?php namespace Defines;

/**
 * User status
 *
 * @note uses in `content`.`access` [read][edit/create][comment]
 *
 * @sample 727 - anyone could read and comment the page; only author,editor and admin could comment.
 * @sample 000 - comments are closed, only admin could view the page
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package Defines
 */
class Users
{

    const ADMIN = 0;

    const AUDITOR = 1;

    const AUTHOR = 2;

    const MODER = 3;

    const TRANSLATOR = 4;

    const AUTHOR_GROUP = 5;

    const AUTHORIZED = 6;

    const NOT_AUTHORIZED = 7;

}
