<?php
namespace PhalApi\Tests;

use PhalApi\Request\Parser;

/**
 * PhpUnderControl_PhalApiRequestVar_Test
 *
 * 针对 ../../PhalApi/Request/Var.php Parser 类的PHPUnit单元测试
 *
 * @author: dogstar 20141012
 */

class PhpUnderControl_PhalApiRequestVar_Test extends \PHPUnit_Framework_TestCase
{
    public $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new Parser();
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

        $rs = Parser::format($varName, $rule, $params);

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatString
     */
    public function testFormatString()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey'), array('testKey' => 2014));

        $this->assertSame('2014', $rs);
    }

    /**
     * @group testFormatStringMinMax
     */
    public function testFormatStringMinMax()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', "max" => 9, 'min' => 9, "format" => 'utf8'), array('testKey' => 'PhalApi测试'));

        $this->assertSame('PhalApi测试', $rs);
    }


    /**
     * @group testFormatStringMinMax
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testFormatStringExceptionMinMax()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', "max" => 8, 'min' => 8, "format" => 'utf8'), array('testKey' => 'PhalApi测试'));

    }

    /**
     * @group testFormatStringMinMax
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 显示此错误
     */
    public function testFormatStringExceptionMinMaxWithMessage()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', "max" => 8, 'min' => 8, "format" => 'utf8', 'message' => '显示此错误'), array('testKey' => 'PhalApi测试'));

    }

    /**
     * @group testFormatString
     * @expectedException \PhalApi\Exception\InternalServerErrorException
     */
    public function testFormatStringWithRuleExceptionMinGtMax()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'min' => 9, 'max' => 5), array('testKey' => '2014'));
    }

    /**
     * @group testFormatString
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testFormatStringWithParamExceptionLtMin()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'min' => 8), array('testKey' => 2014));
    }

    /**
     * @group testFormatString
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 字符串长度过短
     */
    public function testFormatStringWithParamExceptionLtMinWithMessage()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'min' => 8, 'message' => '字符串长度过短'), array('testKey' => '2014'));
    }

    /**
     * @group testFormatString
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testFormatStringWithParamExceptionGtMax()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'max' => 2, );

        $rs = Parser::format(
            'testKey', $rule, array('testKey' => $value));
    }

    /**
     * @group testFormatString
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 字符串长度过长
     */
    public function testFormatStringWithParamExceptionGtMaxWithMessage()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'max' => 2, 'message' => '字符串长度过长');

        $rs = Parser::format(
            'testKey', $rule, array('testKey' => $value));
    }

    /**
     * @group testFormatInt
     */
    public function testFormatInt()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'int'), array('testKey' => 2014));

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatFloat
     */
    public function testFormatFloat()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'float'), array('testKey' => '3.14'));

        $this->assertSame(3.14, $rs);
    }

    /**
     * @dataProvider provideDataForFormatBoolean
     * @group testFormatBoolean
     */
    public function testFormatBoolean($oriValue, $expValue)
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'boolean'), array('testKey' => $oriValue));

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
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp'), array('testKey' => '2014-10-01 12:00:00'));

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }

    /**
     * @group testFormatDate
     */
    public function testFormatDateIllegal()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp'), array('testKey' => '2014-99-99 XX:XX:XX'));
        $this->assertEquals(0, $rs);
    }

    /**
     * @group testFormatDate
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testFormatDateRange()
    {
        $rs = Parser::format(
            'testKey', array('name' => 'testKey', 'type' => 'date', 'format' => 'timestamp', 'max' => 100), array('testKey' => '2014-10-01 12:00:00'));
    }

    /**
     * @group testFormatArray
     */
    public function testFormatArrayWithJson()
    {
        $arr = array('age' => 100, 'sex' => 'male');

        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'array', 'format' => 'json'),
            array('testKey' => json_encode($arr))
        );

        $this->assertSame($arr, $rs);
    }

    public function testFormatArrayWithExplode()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'array', 'format' => 'explode', 'separator' => '|'),
            array('testKey' => '1|2|3|4|5')
        );

        $this->assertEquals(array(1, 2, 3, 4, 5), $rs);
    }

    public function testFormatArrayDefault()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'array'),
            array('testKey' => 'phalapi')
        );

        $this->assertEquals(array('phalapi'), $rs);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatArrayRange()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'array', 'format' => 'explode', 'separator' => '|', 'max' => 3),
            array('testKey' => '1|2|3|4|5')
        );
    }

    /**
     * @group testFile
     */
    public function testFormatFile()
    {
        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/jpeg', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 0);

        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());

        $this->assertEquals($_FILES['aFile'], $rs);
    }

    /**
     * @group testFile
     */
    public function testFormatFileInsensiveCase()
    {
        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/jpeg', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 0);

        $rule = array('name' => 'aFile', 'range' => array('image/JPEG'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());

        $this->assertEquals($_FILES['aFile'], $rs);
    }

    /**
     * @group testFile
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatFileButTooLarge()
    {
        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/jpeg', 'size' => 9999, 'tmp_name' => '/tmp/123456', 'error' => 0);

        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());
    }

    /**
     * @group testFile
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatFileButWrongType()
    {
        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/png', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 0);

        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());
    }

    /**
     * @group testFile
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatFileButError()
    {
        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/png', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 2);

        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());
    }

    /**
     * @group testFile
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatFileEmptyButRequire()
    {
        $rule = array('name' => 'aFile', 'type' => 'file');

        $rs = Parser::format('aFile', $rule, array());
        $this->assertEquals(NULL, $rs);
    }

    /**
     * $group testFile
     */
    public function testFormatFileEmptyWithDefualt()
    {
        $default = array('name' => 'test.txt', 'type' => 'txt', 'tmp_name' => '/tmp/test.txt');
        $rule = array('name' => 'aFile', 'default' => $default, 'type' => 'file');
        $_FILES['aFile'] = null;

        $rs = Parser::format('aFile', $rule, array());
        $this->assertEquals($default, $rs);
    }

    /**
     * @group testFormatEnum
     */
    public function testFormatEnum()
    {
        $rs = Parser::format(
            'testKey', array('range' => array('ios', 'android'), 'type' => 'enum'), array('testKey' => 'ios'));

        $this->assertSame('ios', $rs);
    }

    /**
     * @group testFormatEnum
     * @expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testFormatEnumWithRuleException()
    {
        $rs = Parser::format(
            'testKey', array('type' => 'enum', 'name' => 'testKey'), array('testKey' => 'ios'));
    }

    /**
     * @group testFormatEnum
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testFormatEnumWithParamException()
    {
        $rs = Parser::format(
            'testKey', array('type' => 'enum', 'name' => 'testKey', 'range' => array('ios', 'android')), array('testKey' => 'pc'));
    }

    /**
     * @group testFormatEnum
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 范围不对
     */
    public function testFormatEnumWithParamExceptionWithMessage()
    {
        $rs = Parser::format(
            'testKey', array('type' => 'enum', 'name' => 'testKey', 'range' => array('ios', 'android', 'message' => '范围不对')), array('testKey' => 'pc'));
    }

    public function testFormatAllTypes()
    {
        $params = array(
            'floatVal' => '1.0',
            'booleanVal' => '1',
            'dateVal' => '2015-02-05 00:00:00',
            'arrayVal' => 'a,b,c',
            'enumVal' => 'male',
        );

        $rule = array('name' => 'floatVal', 'type' => 'float');
        $rs = Parser::format('floatVal', $rule,  $params);
        $this->assertSame(1.0, $rs);

        $rule = array('name' => 'booleanVal', 'type' => 'boolean');
        $rs = Parser::format('booleanVal', $rule,  $params);
        $this->assertSame(true, $rs);

        $rule = array('name' => 'dateVal', 'type' => 'date', 'format' => 'timestamp');
        $rs = Parser::format('dateVal', $rule,  $params);
        $this->assertSame( 1423065600, $rs);

        $rule = array('name' => 'arrayVal', 'type' => 'array', 'format' => 'explode');
        $rs = Parser::format('arrayVal', $rule,  $params);
        $this->assertSame(array('a', 'b', 'c'), $rs);

        $rule = array('name' => 'enumVal', 'type' => 'enum', 'range' => array('female', 'male'));
        $rs = Parser::format('enumVal', $rule,  $params);
        $this->assertSame('male', $rs);

        $rule = array('name' => 'noThisKey');
        $rs = Parser::format('noThisKey', $rule,  $params);
        $this->assertSame(null, $rs);

        $rule = array('name' => 'noThisKey', 'type' => 'noThisType');
        $rs = Parser::format('noThisKey', $rule,  $params);
        $this->assertSame(null, $rs);

        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/jpeg', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 0);
        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');
        $rs = Parser::format('aFile', $rule, $params);
        $this->assertNotEmpty($rs);
    }

    /**
     * @expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testGetEnumWithEmptyRange()
    {
        Parser::format('key',  array('name' => 'key', 'type' => 'enum', 'range' => array()), array('key' => 'aHa~'));
    }

    public function testStringWithRegxRight()
    {
        //very simple mobile phone
        $rule = array('name' => 'key', 'type' => 'string', 'regex' => '/^[0-9]{11}/');
        Parser::format('testKey', $rule, array('testKey' => '13800138000'));
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testStringWithRegxWrong()
    {
        $rule = array('name' => 'key', 'type' => 'string', 'regex' => '/^[0-9]{11}/');
        Parser::format('key', $rule, array('key' => 'no a number'));
    }

    public function testFormatCallable()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'callable', 'callback' => array('\\PhalApi\\Tests\\ParserMyCallback', 'go')),
            array('testKey' => 1)
        );

        $this->assertSame(2, $rs);
    }

    /**
     * @expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testFormatCallableButWroing()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'callable', 'callback' => 'xxx'),
            array('testKey' => 1)
        );
    }

    /**
     * @expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testWrongFormat()
    {
        $rs = Parser::format(
            'testKey',
            array('name' => 'testKey', 'type' => 'wrong_type'),
            array('testKey' => 1)
        );

    }
}

class ParserMyCallback {

    public static function go($value, $rule) {
        return $value + 1;
    }
}
