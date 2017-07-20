<?php
namespace PhalApi\Tests;

use PhalApi\Tool;

/**
 * PhpUnderControl_PhalApiTool_Test
 *
 * 针对 ../PhalApi/Tool.php Tool 类的PHPUnit单元测试
 *
 * @author: dogstar 20150212
 */

class PhpUnderControl_PhalApiTool_Test extends \PHPUnit_Framework_TestCase
{
    public $tool;

    protected function setUp()
    {
        parent::setUp();

        $this->tool = new Tool();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetClientIp
     */ 
    public function testGetClientIp()
    {
        $rs = Tool::getClientIp();
    }

    public function testGetClientIpWithEnvMock() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.4';
        $this->assertEquals('127.0.0.4', Tool::getClientIp());

        putenv('REMOTE_ADDR=127.0.0.3');
        $this->assertEquals('127.0.0.3', Tool::getClientIp());

        putenv('HTTP_X_FORWARDED_FOR=127.0.0.2');
        $this->assertEquals('127.0.0.2', Tool::getClientIp());

        putenv('HTTP_CLIENT_IP=127.0.0.1');
        $this->assertEquals('127.0.0.1', Tool::getClientIp());
    }

    /**
     * @group testCreateRandStr
     */ 
    public function testCreateRandStr()
    {
        $len = '5';

        $rs = Tool::createRandStr($len);

        $this->assertEquals($len, strlen($rs));
    }

    public function testCreateDir()
    {
        Tool::createDir("./test/test2/test3");

        $this->assertEquals(true, is_dir("./test/test2/test3"));
        Tool::deleteDir("./test");
    }

    public function testDeleteDir()
    {
        mkdir("./test");
        mkdir("./test/test2");
        file_put_contents("./test/test2/test3","test");
        Tool::deleteDir("./test");

        $this->assertEquals(false, is_dir("./test"));
    }

    public function testArrIndex()
    {

        $arr = array(
            "test" => "test"
        );
        ;

        $this->assertEquals("test", Tool::arrIndex($arr,"test"));
        $this->assertEquals("default", Tool::arrIndex($arr,"test2","default"));
        $this->assertEquals('', Tool::arrIndex($arr,"test3"));
    }

    public function testArrayToXml()
    {
        $arr = array('name' => 'phalapi');

        $rs = Tool::arrayToXml($arr);

        $this->assertContains('phalapi', $rs);
    }

    public function testXmlToArray()
    {
        $arr = array('name' => 'phalapi');

        $rs = Tool::xmlToArray(Tool::arrayToXml($arr));

        $this->assertEquals($arr, $rs);
    }

    public function testTrimSpaceInStr()
    {
        $this->assertEquals('abc', Tool::trimSpaceInStr('a b c'));
    }
}
