<?php
/**
 * PhpUnderControl_PhalApiExceptionRedirect_Test
 *
 * 针对 ../PhalApi/Exception/Redirect.php PhalApi_Exception_Redirect 类的PHPUnit单元测试
 *
 * @author: dogstar 20170730
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Exception_Redirect')) {
    require dirname(__FILE__) . '/../../PhalApi/Exception/Redirect.php';
}

class PhpUnderControl_PhalApiExceptionRedirect_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiExceptionRedirect;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiExceptionRedirect = new PhalApi_Exception_Redirect('phpunit');
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(DI()->tracer->getSqls());
    }

    public function testHere()
    {
        $exp = new PhalApi_Exception_Redirect('just for test');
    }
}
