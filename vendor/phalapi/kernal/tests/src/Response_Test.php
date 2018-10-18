<?php
namespace PhalApi\Tests;

/**
 * PhpUnderControl_PhalApiResponse_Test
 *
 * 针对 ../PhalApi/Response.php PhalApi_Response 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

class PhpUnderControl_PhalApiResponse_Test extends \PHPUnit_Framework_TestCase
{
    public $response;

    protected function setUp()
    {
        parent::setUp();

        $this->response = new JsonResponseMock();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testSetRet
     */ 
    public function testSetRet()
    {
        $ret = '0';

        $rs = $this->response->setRet($ret);
    }

    /**
     * @group testSetData
     */ 
    public function testSetData()
    {
        $data = array('sth' => 'hi~');

        $rs = $this->response->setData($data);
    }

    /**
     * @group testSetMsg
     */ 
    public function testSetMsg()
    {
        $msg = 'this will shoul as a wrong msg';

        $rs = $this->response->setMsg($msg);
    }

    public function testSetDebug()
    {
        $this->response->setDebug('stack', array('Fight~'));
        $this->response->setDebug('sqls', array('SELECT', 'DELETE'));
    }

    /**
     * @group testAddHeaders
     */ 
    public function testAddHeaders()
    {
        $key = 'Content-Type';
        $content = 'text/html;charset=utf-8';

        $rs = $this->response->addHeaders($key, $content);
    }

    public function testGetHeaders()
    {
        $key = 'Version';
        $content = '1.1.2';

        $rs = $this->response->addHeaders($key, $content);

        $this->assertEquals($content, $this->response->getHeaders($key));
        $this->assertTrue(is_array($this->response->getHeaders()));
    }

    /**
     * @group testOutput
     */ 
    public function testOutput()
    {
        $this->response->setRet(404);
        $this->response->setMsg('not found');
        $this->response->setData(array('name' => 'PhalApi'));

        $rs = $this->response->output();
        $this->expectOutputRegex('/"ret":404/');
    }

    public function testOutputEmptyArray()
    {
        $this->response->output();
        $this->expectOutputRegex('/,"data":\{\},/');
    }

    public function testOutputDataZero()
    {
        $this->response->setData(0);
        $this->response->output();
        $this->expectOutputRegex('/,"data":0,/');
    }

    public function testAdustHttpStatus()
    {
        $this->response->adjustHttpStatus();
    }
}
