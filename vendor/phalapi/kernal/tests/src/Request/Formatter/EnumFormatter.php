<?php
namespace PhalApi\Tests;

use PhalApi\Request\Formatter\EnumFormatter;

/**
 * PhpUnderControl_PhalApiRequestFormatterEnum_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Enum.php PhalApi_Request_Formatter_Enum 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

class PhpUnderControl_PhalApiRequestFormatterEnum_Test extends \PHPUnit_Framework_TestCase
{
    public $enumFomatter;

    protected function setUp()
    {
        parent::setUp();

        $this->enumFomatter = new EnumFormatter();
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

        $rs = $this->enumFomatter->parse($value, $rule);

        $this->assertEquals('ios', $rs);
    }

}
