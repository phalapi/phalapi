<?php
namespace PhalApi\Tests;

use PhalApi\Request\Formatter\ArrayFormatter;

/**
 * PhpUnderControl_PhalApiRequestFormatterArray_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Array.php PhalApi_Request_Formatter_Array 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

class PhpUnderControl_PhalApiRequestFormatterArray_Test extends \PHPUnit_Framework_TestCase
{
    public $arrayFormatter;

    protected function setUp()
    {
        parent::setUp();

        $this->arrayFormatter = new ArrayFormatter();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = '1|2|3|4|5';
        $rule = array('name' => 'testKey', 'type' => 'array', 'format' => 'explode', 'separator' => '|');

        $rs = $this->arrayFormatter->parse($value, $rule);

        $this->assertTrue(is_array($rs));
        $this->assertEquals(array(1, 2, 3, 4, 5), $rs);
    }

    public function testParseJson()
    {
        $value = array('a' => 1);
        $rule = array('name' => 'testKey', 'type' => 'array', 'format' => 'json');

        $rs = $this->arrayFormatter->parse(json_encode($value), $rule);

        $this->assertTrue(is_array($rs));
        $this->assertEquals($value, $rs);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testParseWrongJson()
    {
        $rule = array('name' => 'testKey', 'type' => 'array', 'format' => 'json');

        $rs = $this->arrayFormatter->parse('{"a":1', $rule);
    }
}
