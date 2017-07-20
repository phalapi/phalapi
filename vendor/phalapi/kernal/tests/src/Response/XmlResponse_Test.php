<?php

/**
 * PhpUnderControl_PhalApi\Response\XmlResponse_Test
 *
 * 针对 ../src/Response/XmlResponse.php PhalApi\Response\XmlResponse 类的PHPUnit单元测试
 *
 * @author: dogstar 20170715
 */

class PhpUnderControl_PhalApiResponseXmlResponse_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiResponseXmlResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseXmlResponse = new PhalApi\Response\XmlResponse();
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
        $this->phalApiResponseXmlResponse->output();
        $this->expectOutputRegex('/xml/');
    }
}
