<?php namespace Tests;

/**
 * Basic functionality for tests
 *
 * @author s.lyskovski
 */
class ListScope extends Simple
{

    /**
     * @var \Defines\ListInterface
     */
    protected $object;

    /**
     * Check that required interface exists (with getDefault and getList)
     */
    final public function testInterface()
    {
        $this->assertInstanceOf('Defines\ListInterface', $this->object);
    }

    /**
     * Check that getList provide ALL constants
     * @note for hacked list has to be used another function's name
     */
    final public function testCheckGetList()
    {
        $oClass = $this->object;
        $aCompare = (new \System\Aggregator)->const2array($oClass);
        $this->assertEquals($aCompare, $oClass::getList());
    }

}
