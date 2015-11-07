<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterEnum_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Enum.php PhalApi_Request_Formatter_Enum 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Request_Formatter_Enum')) {
    require dirname(__FILE__) . '/../../../PhalApi/Request/Formatter/Enum.php';
}

class PhpUnderControl_PhalApiRequestFormatterEnum_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiRequestFormatterEnum;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiRequestFormatterEnum = new PhalApi_Request_Formatter_Enum();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = 'ios';
        $rule = array('range' => array('ios', 'android'));

        $rs = $this->phalApiRequestFormatterEnum->parse($value, $rule);

        $this->assertEquals('ios', $rs);
    }

}
