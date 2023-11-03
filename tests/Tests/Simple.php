<?php namespace Tests;

use \Engine\Request\Input;

/**
 * Basic functionality for tests
 *
 * To fiz this you need to tell PHPUnit not to preserve global state between tests
 * and to run each test in a separate process:
 *     protected $preserveGlobalState = false;
 *     protected $runTestInSeparateProcess = true;
 *
 * @author Viachaslau Lyskouski
 */
class Simple extends \PHPUnit_Framework_TestCase
{

    /**
     * Restore SERVER parameters
     */
    protected function restoreServer()
    {
        $aServer = array(
            'APPLICATION_ENV' => \Defines\ServerType::TEST,
            'SITE' => 'localhost',
            'SERVER_NAME' => 'localhost',
            'HTTP_HOST' => 'localhost'
        );
        (new Input)->fake($aServer, INPUT_SERVER);
        \System\Registry::setCron(false);
    }

    /**
     * Open protected or private method
     *
     * @param string $methodName
     */
    protected function callMethod($methodName, $attr = array())
    {
        $reflect = new \ReflectionClass($this->object);
        $method = $reflect->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->object, $attr);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->restoreServer();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $oRequest = new Input;
        $oRequest->clearOverride(INPUT_GET);
        $oRequest->clearOverride(INPUT_POST);
        $oRequest->clearOverride(INPUT_COOKIE);
        $oRequest->clearOverride(INPUT_SESSION);
        $this->restoreServer();
    }

}
