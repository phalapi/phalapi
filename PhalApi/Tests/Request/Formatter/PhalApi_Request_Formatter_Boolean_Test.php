<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterBoolean_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Boolean.php PhalApi_Request_Formatter_Boolean 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Request_Formatter_Boolean')) {
    require dirname(__FILE__) . '/../../../PhalApi/Request/Formatter/Boolean.php';
}

class PhpUnderControl_PhalApiRequestFormatterBoolean_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiRequestFormatterBoolean;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiRequestFormatterBoolean = new PhalApi_Request_Formatter_Boolean();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = 'on';
        $rule = array();

        $rs = $this->phalApiRequestFormatterBoolean->parse($value, $rule);

        $this->assertTrue($rs);
    }

}
