<?php
/**
 * PhalApi_PhalApi\Error\ApiError_Test
 *
 * 针对 ../src/Error/ApiError.php PhalApi\Error\ApiError 类的PHPUnit单元测试
 *
 * @author: dogstar 20200325
 */

namespace tests\PhalApi\Error;
use PhalApi\Error\ApiError;

class PhpUnderControl_PhalApiErrorApiError_Test extends \PHPUnit\Framework\TestCase
{
    public $phalApiErrorApiError;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiErrorApiError = new \PhalApi\Error\ApiError();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }


    /**
     * @group testHandleError
     */ 
    public function testHandleError()
    {
        $errno = E_DEPRECATED;
        $errstr = 'phpunit test';
        $errfile = './src/Error/ApiError_Test.php';
        $errline = 30;

        $rs = $this->phalApiErrorApiError->handleError($errno, $errstr, $errfile, $errline);
    }

    public function testHandleErrorWarning() 
    {
        echo $aaa;
    }

}
