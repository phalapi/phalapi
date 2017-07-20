<?php

//require_once dirname(__FILE__) . '/bootstrap.php';

if (!class_exists('PhalApi\\Response\\ExplorerResponse')) {
    require dirname(__FILE__) . '/./src/Response/ExplorerResponse.php';
}

/**
 * PhpUnderControl_PhalApi\Response\ExplorerResponse_Test
 *
 * 针对 ./src/Response/ExplorerResponse.php PhalApi\Response\ExplorerResponse 类的PHPUnit单元测试
 *
 * @author: dogstar 20170708
 */

class PhpUnderControl_PhalApiResponseExplorerResponse_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiResponseExplorerResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseExplorerResponse = new PhalApi\Response\ExplorerResponse();
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
        $this->phalApiResponseExplorerResponse->output();
    }
}
