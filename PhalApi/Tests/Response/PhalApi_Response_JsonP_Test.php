<?php
/**
 * PhpUnderControl_PhalApiResponseJsonP_Test
 *
 * 针对 ../PhalApi/Response/JsonP.php PhalApi_Response_JsonP 类的PHPUnit单元测试
 *
 * @author: dogstar 20170805
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Response_JsonP')) {
    require dirname(__FILE__) . '/../../PhalApi/Response/JsonP.php';
}

class PhpUnderControl_PhalApiResponseJsonP_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiResponseJsonP;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseJsonP = new PhalApi_Response_JsonP('foo');

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $this->phalApiResponseJsonP = new PhalApi_Response_JsonP('foo', JSON_UNESCAPED_UNICODE);
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
        $this->phalApiResponseJsonP->setData(array('我爱中国'));

        $this->phalApiResponseJsonP->output();
    }
}
