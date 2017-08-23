<?php

//require_once dirname(__FILE__) . '/bootstrap.php';

if (!class_exists('PhalApi\\Response\\JsonpResponse')) {
    require dirname(__FILE__) . '/./src/Response/JsonpResponse.php';
}

/**
 * PhpUnderControl_PhalApi\Response\JsonpResponse_Test
 *
 * 针对 ./src/Response/JsonpResponse.php PhalApi\Response\JsonpResponse 类的PHPUnit单元测试
 *
 * @author: dogstar 20170708
 */

class PhpUnderControl_PhalApiResponseJsonpResponse_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiResponseJsonpResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseJsonpResponse = new PhalApi\Response\JsonpResponse('foo');
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $this->phalApiResponseJsonpResponse = new PhalApi\Response\JsonpResponse('foo', JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        }
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(DI()->tracer->getSqls());
    }

    public function testOutput()
    {
        $this->phalApiResponseJsonpResponse->setData(array('我爱中国'));
        $this->phalApiResponseJsonpResponse->output();
        $this->expectOutputRegex('/foo/');
    } 

}
