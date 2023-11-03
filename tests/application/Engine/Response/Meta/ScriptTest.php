<?php namespace Engine\Response\Meta;

use MatthiasMullie\Minify\JS;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-23 at 12:55:31.
 */
class ScriptTest extends \Tests\Simple
{

    /**
     * @var Script
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Script;
    }

    /**
     * @covers Engine\Response\Meta\Script::getSrc
     * @covers Engine\Response\Meta\Script::__construct
     */
    public function testGetSrc()
    {
        $this->object = new Script('test');
        $this->assertEquals('test.js', $this->object->getSrc());
    }

    /**
     * @covers Engine\Response\Meta\Script::__construct
     * @covers Engine\Response\Meta\Script::getSrc
     * @covers Engine\Response\Meta\Script::getContent
     */
    public function testGetContent()
    {

        $this->object = new Script('test', 'test content');
        $this->assertEquals('', $this->object->getSrc());
        $this->assertEquals('test content', $this->object->getContent());
    }

    /**
     * @covers Engine\Response\Meta\Script::isEqual
     */
    public function testIsEqual()
    {
        $o = new Script('test');
        $this->assertTrue($o->isEqual($o));
        $o2 = new Script('', 'test');
        $this->assertFalse($o->isEqual($o2));
        $this->assertFalse($o2->isEqual($o2));
    }
}
