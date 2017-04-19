<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterDate_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Date.php PhalApi_Request_Formatter_Date 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Request_Formatter_Date')) {
    require dirname(__FILE__) . '/../../../PhalApi/Request/Formatter/Date.php';
}

class PhpUnderControl_PhalApiRequestFormatterDate_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiRequestFormatterDate;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiRequestFormatterDate = new PhalApi_Request_Formatter_Date();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = '2014-10-01 12:00:00';
        $rule = array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp');

        $rs = $this->phalApiRequestFormatterDate->parse($value, $rule);

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }

    public function testParseWithMinAndMax()
    {
        $value = '2014-10-01 12:00:00';
        $rule = array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp', 'min' => strtotime('2014-10-01 00:00:00'), 'max' => strtotime('2014-10-01 23:59:59'));

        $rs = $this->phalApiRequestFormatterDate->parse($value, $rule);

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }

    public function testParseWithMinAndMaxForString()
    {
        $value = '2014-10-01 12:00:00';
        $rule = array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp', 'min' => '2014-10-01 00:00:00', 'max' => '2014-10-01 23:59:59');

        $rs = $this->phalApiRequestFormatterDate->parse($value, $rule);

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }
}
