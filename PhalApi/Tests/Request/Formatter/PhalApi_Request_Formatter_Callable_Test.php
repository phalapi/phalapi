<?php
/**
 * PhpUnderControl_PhalApiRequestFormatterCallable_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/Callable.php PhalApi_Request_Formatter_Callable 类的PHPUnit单元测试
 *
 * @author: dogstar 20151107
 */

require_once dirname(__FILE__) . '/../../test_env.php';

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
    }

}

function callbackForFormatterTest($value, $rule, $params) {
    echo "got you!";
    //var_dump($value, $rule, $params);
}
