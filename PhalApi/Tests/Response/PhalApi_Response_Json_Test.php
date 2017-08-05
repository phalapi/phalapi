<?php
/**
 * PhpUnderControl_PhalApiResponseJson_Test
 *
 * 针对 ../PhalApi/Response/Json.php PhalApi_Response_Json 类的PHPUnit单元测试
 *
 * @author: dogstar 20170805
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Response_Json')) {
    require dirname(__FILE__) . '/../../PhalApi/Response/Json.php';
}

class PhpUnderControl_PhalApiResponseJson_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiResponseJson;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponseJson = new PhalApi_Response_Json(JSON_FORCE_OBJECT);

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $this->phalApiResponseJson = new PhalApi_Response_Json(JSON_UNESCAPED_UNICODE);
        }
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
        $this->phalApiResponseJson->setData(array('我爱中国'));
        $this->phalApiResponseJson->output();
    }

}
