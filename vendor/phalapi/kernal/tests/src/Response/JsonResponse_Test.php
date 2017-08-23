<?php

//require_once dirname(__FILE__) . '/bootstrap.php';

if (!class_exists('PhalApi\\Response\\JsonResponse')) {
    require dirname(__FILE__) . '/./src/Response/JsonResponse.php';
}

/**
 * PhpUnderControl_PhalApi\Response\JsonResponse_Test
 *
 * 针对 ./src/Response/JsonResponse.php PhalApi\Response\JsonResponse 类的PHPUnit单元测试
 *
 * @author: dogstar 20170708
 */

class PhpUnderControl_PhalApiResponseJsonResponse_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiResponseJsonResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseJsonResponse = new PhalApi\Response\JsonResponse();

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $this->phalApiResponseJsonResponse = new PhalApi\Response\JsonResponse(JSON_UNESCAPED_UNICODE);
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
        $this->phalApiResponseJsonResponse->setData(array('我爱中国'));
        $this->phalApiResponseJsonResponse->output();
        $this->expectOutputRegex('/200/');
    } 
}
