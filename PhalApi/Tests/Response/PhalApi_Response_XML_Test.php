<?php
/**
 * PhpUnderControl_PhalApiResponseXML_Test
 *
 * 针对 ./PhalApi/Response/Xml.php PhalApi_Response_XML 类的PHPUnit单元测试
 *
 * @author: dogstar 20170715
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Response_XML')) {
    require dirname(__FILE__) . '/../../PhalApi/Response/Xml.php';
}

class PhpUnderControl_PhalApiResponseXML_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiResponseXML;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseXML = new PhalApi_Response_XML();
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
        $data = array('name' => 'phalapi');
        $this->phalApiResponseXML->setData($data)->output();

        $this->expectOutputRegex('/xml/');        
    }
}
