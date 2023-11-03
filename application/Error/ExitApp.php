<?php namespace Error;

/**
 * Stop application execution
 * @note SHOULD be used ONLY as:
 * @sample (new \System\Handler)->stopApplication();
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
class ExitApp extends \Exception
{

}
