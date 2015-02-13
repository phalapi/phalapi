<?php
/**
 * PhpUnderControl_PhalApiTool_Test
 *
 * 针对 ../PhalApi/Tool.php PhalApi_Tool 类的PHPUnit单元测试
 *
 * @author: dogstar 20150212
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Tool')) {
    require dirname(__FILE__) . '/../PhalApi/Tool.php';
}

class PhpUnderControl_PhalApiTool_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiTool;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiTool = new PhalApi_Tool();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetClientIp
     */ 
    public function testGetClientIp()
    {
        $rs = PhalApi_Tool::getClientIp();
    }

    public function testGetClientIpWithEnvMock() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.4';
        $this->assertEquals('127.0.0.4', PhalApi_Tool::getClientIp());

        putenv('REMOTE_ADDR=127.0.0.3');
        $this->assertEquals('127.0.0.3', PhalApi_Tool::getClientIp());

        putenv('HTTP_X_FORWARDED_FOR=127.0.0.2');
        $this->assertEquals('127.0.0.2', PhalApi_Tool::getClientIp());

        putenv('HTTP_CLIENT_IP=127.0.0.1');
        $this->assertEquals('127.0.0.1', PhalApi_Tool::getClientIp());
    }

    /**
     * @group testCreateRandStr
     */ 
    public function testCreateRandStr()
    {
        $len = '5';

        $rs = PhalApi_Tool::createRandStr($len);

        $this->assertEquals($len, strlen($rs));
    }

}
