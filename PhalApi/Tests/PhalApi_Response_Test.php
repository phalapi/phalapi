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
    public $phalApiResponse;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiResponse = new PhalApi_Response_Json_Mock();
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

        $rs = $this->phalApiResponse->setRet($ret);
    }

    /**
     * @group testSetData
     */ 
    public function testSetData()
    {
        $data = array('sth' => 'hi~');

        $rs = $this->phalApiResponse->setData($data);
    }

    /**
     * @group testSetMsg
     */ 
    public function testSetMsg()
    {
        $msg = 'this will shoul as a wrong msg';

        $rs = $this->phalApiResponse->setMsg($msg);
    }

    public function testSetDebug()
    {
        $this->phalApiResponse->setDebug('stack', array('Fight~'));
        $this->phalApiResponse->setDebug('sqls', array('SELECT', 'DELETE'));
    }

    /**
     * @group testAddHeaders
     */ 
    public function testAddHeaders()
    {
        $key = 'Content-Type';
        $content = 'text/html;charset=utf-8';

        $rs = $this->phalApiResponse->addHeaders($key, $content);
    }

    public function testGetHeaders()
    {
        $key = 'Version';
        $content = '1.1.2';

        $rs = $this->phalApiResponse->addHeaders($key, $content);

        $this->assertEquals($content, $this->phalApiResponse->getHeaders($key));
        $this->assertTrue(is_array($this->phalApiResponse->getHeaders()));
    }

    /**
     * @group testOutput
     */ 
    public function testOutput()
    {
        $this->phalApiResponse->setRet(404);
        $this->phalApiResponse->setMsg('not found');
        $this->phalApiResponse->setData(array('name' => 'PhalApi'));

        $rs = $this->phalApiResponse->output();
        $this->expectOutputRegex('/"ret":404/');
    }

    public function testAdustHttpStatus()
    {
        $this->phalApiResponse->adjustHttpStatus();
    }
}
