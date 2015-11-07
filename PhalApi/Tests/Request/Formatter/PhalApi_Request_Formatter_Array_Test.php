<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterArray_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Array.php PhalApi_Request_Formatter_Array 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Request_Formatter_Array')) {
    require dirname(__FILE__) . '/../../../PhalApi/Request/Formatter/Array.php';
}

class PhpUnderControl_PhalApiRequestFormatterArray_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiRequestFormatterArray;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiRequestFormatterArray = new PhalApi_Request_Formatter_Array();
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

        $rs = $this->phalApiRequestFormatterArray->parse($value, $rule);

        $this->assertTrue(is_array($rs));
        $this->assertEquals(array(1, 2, 3, 4, 5), $rs);
    }

}
