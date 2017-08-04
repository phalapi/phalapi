<?php
namespace PhalApi\Tests;

use PhalApi\Request\Formatter\BooleanFormatter;

/**
 * PhpUnderControl_PhalApiRequestFormatterBoolean_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Boolean.php PhalApi_Request_Formatter_Boolean 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

class PhpUnderControl_PhalApiRequestFormatterBoolean_Test extends \PHPUnit_Framework_TestCase
{
    public $booleanFormatter;

    protected function setUp()
    {
        parent::setUp();

        $this->booleanFormatter = new BooleanFormatter();
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

        $rs = $this->booleanFormatter->parse($value, $rule);

        $this->assertTrue($rs);
    }

}
