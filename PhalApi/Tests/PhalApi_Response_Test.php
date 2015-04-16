<?php
/**
 * PhpUnderControl_PhalApiResponse_Test
 *
 * 针对 ../PhalApi/Response.php PhalApi_Response 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Response')) {
    require dirname(__FILE__) . '/../PhalApi/Response.php';
}

class PhpUnderControl_PhalApiResponse_Test extends PHPUnit_Framework_TestCase
{
    public $coreResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->coreResponse = new PhalApi_Response_Json_Mock();
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

        $rs = $this->coreResponse->setRet($ret);
    }

    /**
     * @group testSetData
     */ 
    public function testSetData()
    {
        $data = array('sth' => 'hi~');

        $rs = $this->coreResponse->setData($data);
    }

    /**
     * @group testSetMsg
     */ 
    public function testSetMsg()
    {
        $msg = 'this will shoul as a wrong msg';

        $rs = $this->coreResponse->setMsg($msg);
    }

    /**
     * @group testAddHeaders
     */ 
    public function testAddHeaders()
    {
        $key = 'Content-Type';
        $content = 'text/html;charset=utf-8';

        $rs = $this->coreResponse->addHeaders($key, $content);
    }

    public function testGetHeaders()
    {
        $key = 'Version';
        $content = '1.1.2';

        $rs = $this->coreResponse->addHeaders($key, $content);

        $this->assertEquals($content, $this->coreResponse->getHeaders($key));
        $this->assertTrue(is_array($this->coreResponse->getHeaders()));
    }

    /**
     * @group testOutput
     */ 
    public function testOutput()
    {
        $this->coreResponse->setRet(404);
        $this->coreResponse->setMsg('not found');
        $this->coreResponse->setData(array('name' => 'PhalApi'));

        $rs = $this->coreResponse->output();
        $this->expectOutputString('{"ret":404,"data":{"name":"PhalApi"},"msg":"not found"}');
    }

}
