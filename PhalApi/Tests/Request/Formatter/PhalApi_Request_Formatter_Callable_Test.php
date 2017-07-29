<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterCallable_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Callable.php PhalApi_Request_Formatter_Callable 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';
require_once dirname(__FILE__) . '/Classes/FormatterCallbackMyClass.php';
require_once dirname(__FILE__) . '/Classes/FormatterCallbackMyClass2.php';

if (!class_exists('PhalApi_Request_Formatter_Callable')) {
    require dirname(__FILE__) . '/../../../PhalApi/Request/Formatter/Callable.php';
}

class PhpUnderControl_PhalApiRequestFormatterCallable_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiRequestFormatterCallable;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiRequestFormatterCallable = new PhalApi_Request_Formatter_Callable();

        DI()->loader->addDirs('Request/Formatter/Classes');
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = '1';
        $rule = array('callback' => 'callbackForFormatterTest', 'params' => '11.11', 'name' => 'aKey');

        $rs = $this->phalApiRequestFormatterCallable->parse($value, $rule);
        $this->assertEquals('1_fun', $rs);
    }

    public function testParseStaticClassCallType2()
    {
        $value = '1';
        $rule = array('callback' => array('FormatterCallbackMyClass2', 'handle'), 'name' => 'rs');

        $rs = $this->phalApiRequestFormatterCallable->parse($value, $rule);
        $this->assertEquals('1_handle2', $rs);
    }

    public function testParseStaticClassCallType4()
    {
        $value = '1';
        $rule = array('callable' => 'FormatterCallbackMyClass::handle', 'name' => 'rs');

        $rs = $this->phalApiRequestFormatterCallable->parse($value, $rule);
        $this->assertEquals('1_handle', $rs);
    }

    public function testParseInstanceClassCall()
    {
        $value = '1';
        $rule = array('callable' => array(new FormatterCallbackMyClass(), 'handle'), 'name' => 'rs');

        $rs = $this->phalApiRequestFormatterCallable->parse($value, $rule);
        $this->assertEquals('1_handle', $rs);
    }

    public function testParseWithClouser()
    {
        $value = '1';
        $aCallback = function ($value, $rule) { 
            return $value . '_clouser';
        };
        $rule = array('callback' => $aCallback, 'name' => 'rs');

        $rs = $this->phalApiRequestFormatterCallable->parse($value, $rule);
        $this->assertEquals('1_clouser', $rs);
    }
}

function callbackForFormatterTest($value, $rule, $params) {
    //echo "got you!";
    //var_dump($value, $rule, $params);
    return $value . '_fun';
}
