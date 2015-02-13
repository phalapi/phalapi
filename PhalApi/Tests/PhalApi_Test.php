<?php
/**
 * PhpUnderControl_PhalApi_Test
 *
 * 针对 ../PhalApi.php PhalApi 类的PHPUnit单元测试
 *
 * @author: dogstar 20150209
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi')) {
    require dirname(__FILE__) . '/../PhalApi.php';
}

class PhpUnderControl_PhalApi_Test extends PHPUnit_Framework_TestCase
{
    public $phalApi;

    protected function setUp()
    {
        parent::setUp();

        $data = array(
            'service' => 'AnotherImpl.doSth',
        );

        DI()->request = new PhalApi_Request($data);

        $this->phalApi = new PhalApi();
    }

    protected function tearDown()
    {
        DI()->response = 'PhalApi_Response_Json';
    }

    /**
     * @group testResponse
     */ 
    public function testResponseWithJsonMock()
    {
        DI()->response = 'PhalApi_Response_Json_Mock';

        $rs = $this->phalApi->response();

        $rs->output();

        $this->expectOutputString('{"ret":200,"data":"hello wolrd!","msg":""}');
    }

    /**
     * @group testResponse
     */ 
    public function testResponseWithJsonPMock()
    {
        DI()->response = new PhalApi_Response_JsonP_Mock('test');

        $rs = $this->phalApi->response();

        $rs->output();

        $this->expectOutputString('test({"ret":200,"data":"hello wolrd!","msg":""})');
    }

    /**
     * @group testResponse
     */ 
    public function testResponseWithExplorer()
    {
        DI()->response = 'PhalApi_Response_Explorer';

        $rs = $this->phalApi->response();

        $rs->output();

        $expRs = array (
            'ret' => 200,
            'data' => 'hello wolrd!',
            'msg' => '',
        );

        $this->assertEquals($expRs, $rs->getResult());
    }

    public function testResponseWithBadRequest() {
        $data = array(
            'service' => 'AnotherImpl',
        );

        DI()->request = new PhalApi_Request($data);
        DI()->response = 'PhalApi_Response_Json_Mock';

        $phalApi = new PhalApi();

        $rs = $phalApi->response();

        $rs->output();

        $this->expectOutputRegex('/"ret":400/');
    }

    /**
     * @expectedException Exception
     */
    public function testResponseWithException() {
        $data = array(
            'service' => 'AnotherImpl.MakeSomeTrouble',
        );

        DI()->request = new PhalApi_Request($data);

        $rs = $this->phalApi->response();
    }
}

class Api_AnotherImpl extends PhalApi_Api {

    public function doSth() {
        return 'hello wolrd!';
    }

    public function makeSomeTrouble() {
        throw new Exception('as u can see, i mean to make some trouble');
    }
}
