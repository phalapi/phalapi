<?php

//require_once dirname(__FILE__) . '/bootstrap.php';

if (!class_exists('PhalApi\\Exception\\RedirectException')) {
    require dirname(__FILE__) . '/./src/Exception/RedirectException.php';
}

/**
 * PhpUnderControl_PhalApi\Exception\RedirectException_Test
 *
 * 针对 ./src/Exception/RedirectException.php PhalApi\Exception\RedirectException 类的PHPUnit单元测试
 *
 * @author: dogstar 20170708
 */

class PhpUnderControl_PhalApiExceptionRedirectException_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiExceptionRedirectException;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiExceptionRedirectException = new PhalApi\Exception\RedirectException('test', 0);
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
        $this->assertEquals(300, $this->phalApiExceptionRedirectException->getCode());
    }

}
