<?php namespace Modules\Mind\Trainer\Gibberish;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-12-26 at 13:26:28.
 */
class ModelTest extends \Tests\Simple
{

    /**
     * @var Model
     */
    protected $object;

    /**
     * @var string
     */
    protected $lang;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Model;
        $this->lang = \System\Registry::translation()->getTargetLanguage();
        \System\Registry::translation()->setTargetLanguage(\Defines\Language::EN);
    }

    protected function tearDown()
    {
        parent::tearDown();
        \System\Registry::translation()->setTargetLanguage($this->lang);
    }

    /**
     * @covers Modules\Mind\Trainer\Gibberish\Model::getGameAttr
     * @todo   Implement testGetGameAttr().
     */
    public function testGetGameAttr()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modules\Mind\Trainer\Gibberish\Model::getGameRating
     * @todo   Implement testGetGameRating().
     */
    public function testGetGameRating()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modules\Mind\Trainer\Gibberish\Model::startGame
     */
    public function testStartGame()
    {
        $res = $this->callMethod('startGame', [0]);
        $this->assertInternalType('array', $res);
        $this->assertCount(100, $res['content']);
        $this->assertCount(1, $res['target']);
        $this->assertSame(50, $res['count']);

        $a = array_count_values($res['content']);
        $num = 0;
        foreach ($res['target'] as $key) {
            $num += $a[$key];
        }
        $this->assertSame(50, $num);
    }

    /**
     * @covers Modules\Mind\Trainer\Gibberish\Model::startGame
     */
    public function testStartGame5Lvl()
    {
        $res = $this->callMethod('startGame', [5]);
        $this->assertInternalType('array', $res);
        $this->assertCount(600, $res['content']);
        $this->assertCount(6, $res['target']);
        $this->assertSame(75, $res['count']);

        $a = array_count_values($res['content']);
        $num = 0;
        foreach ($res['target'] as $key) {
            $num += $a[$key];
        }
        $this->assertSame(75, $num);
    }

}
