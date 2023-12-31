<?php namespace System\Converter\Helper;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-03-16 at 10:30:08.
 */
class HtmlTest extends \Tests\Simple
{

    /**
     * @var Html
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Html;
    }

    protected function cover($txt)
    {
        return trim(str_replace(["\r", "\n"], '', $txt));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Script()
    {
        $test = '<strong title="test" style="color:red;backgound:silver" onMouseOver="alert(\'Hello!\')" onclick="alert(\'Hello!\')">Hello</strong><script>alert(\'Hello!\');</script><div>Hello</div>';
        $this->assertEquals('<strong title="test">Hello</strong><p>Hello</p>', $this->cover($this->object->filter($test)));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Div()
    {
        $test = '<strong title="test" style="color:red;backgound:silver" onMouseOver="alert(\'Hello!\')" onclick="alert(\'Hello!\')">Hello</strong><script>alert(\'Hello!\');</script><div>Hello</div>';
        $this->assertEquals('<strong title="test">Hello</strong><div>Hello</div>', $this->cover($this->object->filter($test, ['div'])));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Inline()
    {
        $test = '<strong>Hello</strong><div>Test <div>Hello</div></div>';
        $this->assertEquals('<strong>Hello</strong><p>Test<p>Hello</p></p>', $this->cover($this->object->filter($test)));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_P()
    {
        $test = '<p>Hello<br/>Hello<br/>Hello<br/>Hello</p>';
        $this->assertEquals($test, $this->cover($this->object->filter($test)));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Br()
    {
        $test = '<span>Hello<br/>Hello</span>';
        $this->assertEquals('<span>Hello<br/>Hello</span>', $this->cover($this->object->filter($test)));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Empty()
    {
        $test = '<strong></strong>';
        $this->assertEquals('', $this->object->filter($test));
    }

    /**
     * @covers System\Converter\Helper\Html::filter
     */
    public function testFilter_Utf8()
    {
        $test = '<strong>Тест</strong>';
        $this->assertEquals($test, $this->cover($this->object->filter($test)));
    }

    /**
     * @covers System\Converter\Helper\Html::repair
     */
    public function testRepair()
    {
        $test = '<strong cool="maybe">Hello</b>';
        $this->assertEquals('<strong>Hello</strong>', $this->cover($this->object->repair($test)));
    }
}
