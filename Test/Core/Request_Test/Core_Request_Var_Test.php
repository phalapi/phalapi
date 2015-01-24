<?php
/**
 * PhpUnderControl_CoreRequestVar_Test
 *
 * 针对 ../../../Core/Request/Var.php Core_Request_Var 类的PHPUnit单元测试
 *
 * @author: dogstar 20141012
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('Core_Request_Var')) {
    require dirname(__FILE__) . '/../../../Core/Request/Var.php';
}

class PhpUnderControl_CoreRequestVar_Test extends PHPUnit_Framework_TestCase
{
    public $coreRequestVar;

    protected function setUp()
    {
        parent::setUp();

        $this->coreRequestVar = new Core_Request_Var();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testFormat
     */ 
    public function testFormat()
    {
        $varName = 'testKey';
        $rule = array('type' => 'int', 'default' => '2014');
        $params = array();

        $rs = Core_Request_Var::format($varName, $rule, $params);

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatString
     */ 
    public function testFormatString()
    {
        $value = 2014;
        $rule = array('name' => 'testKey');

        $rs = Core_Request_Var::formatString($value, $rule);

        $this->assertSame('2014', $rs);
    }


    /**
     * @group testFormatString
     * @expectedException Core_Exception_RuleError
     */
    public function testFormatStringWithRuleExceptionMinGtMax()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'min' => 9, 'max' => 5);

        $rs = Core_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatString
     * @expectedException Core_Exception_IllegalParam
     */
    public function testFormatStringWithParamExceptionLtMin()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'min' => 8, );

        $rs = Core_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatString
     * @expectedException Core_Exception_IllegalParam
     */
    public function testFormatStringWithParamExceptionGtMax()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'max' => 2, );

        $rs = Core_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatInt
     */ 
    public function testFormatInt()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', );

        $rs = Core_Request_Var::formatInt($value, $rule);

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatFloat
     */ 
    public function testFormatFloat()
    {
        $value = '3.14';
        $rule = array('name' => 'testKey', );

        $rs = Core_Request_Var::formatFloat($value, $rule);

        $this->assertSame(3.14, $rs);
    }

    /**
     * @dataProvider provideDataForFormatBoolean
     * @group testFormatBoolean
     */ 
    public function testFormatBoolean($oriValue, $expValue)
    {
        $value = $oriValue;
        $rule = array();

        $rs = Core_Request_Var::formatBoolean($value, $rule);

        $this->assertSame($expValue, $rs);
    }

    public function provideDataForFormatBoolean()
    {
        return array(
            array('on', true),
            array('yes', true),
            array('true', true),
            array('success', true),
            array('false', false),
            array('1', true),
            );
    }

    /**
     * @group testFormatDate
     */ 
    public function testFormatDate()
    {
        $value = '2014-10-01 12:00:00';
        $rule = array('format' => 'timestamp');

        $rs = Core_Request_Var::formatDate($value, $rule);

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }

    /**
     * @group testFormatArray
     */ 
    public function testFormatArrayWithJson()
    {
        $arr = array('age' => 100, 'sex' => 'male');
        $value = json_encode($arr);
        $rule = array('format' => 'json');

        $rs = Core_Request_Var::formatArray($value, $rule);

        $this->assertSame($arr, $rs);
    }

    public function testFormatArrayWithExplode()
    {
        $value = '1|2|3|4|5';
        $rule = array('format' => 'explode', 'separator' => '|');

        $rs = Core_Request_Var::formatArray($value, $rule);

        $this->assertEquals(array(1, 2, 3, 4, 5), $rs);
    }

    /**
     * @group testFormatFixed
     */ 
    public function testFormatFixed()
    {
        $value = '2014';
        $rule = array('default' => '2010');

        $rs = Core_Request_Var::formatFixed($value, $rule);

        $this->assertSame('2010', $rs);
    }

    /**
     * @group testFormatEnum
     */ 
    public function testFormatEnum()
    {
        $value = 'ios';
        $rule = array('range' => array('ios', 'android'));

        $rs = Core_Request_Var::formatEnum($value, $rule);

        $this->assertSame('ios', $rs);
    }

    /**
     * @group testFormatEnum
     * @expectedException Core_Exception_RuleError
     */
    public function testFormatEnumWithRuleException()
    {
        $value = 'ios';
        $rule = array('name' => 'testKey');

        $rs = Core_Request_Var::formatEnum($value, $rule);
    }

    /**
     * @group testFormatEnum
     * @expectedException Core_Exception_IllegalParam
     */
    public function testFormatEnumWithParamException()
    {
        $value = 'pc';
        $rule = array('name' => 'testKey', 'range' => array('ios', 'android'));

        $rs = Core_Request_Var::formatEnum($value, $rule);
    }

}
