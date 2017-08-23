<?php

namespace PhalApi\Tests;

use PhalApi\CUrl;

/**
 * PhpUnderControl_PhalApiCUrl_Test
 *
 * 针对 ../PhalApi/CUrl.php PhalApi_CUrl 类的PHPUnit单元测试
 *
 * @author: dogstar 20150415
 */

class PhpUnderControl_PhalApiCUrl_Test extends \PHPUnit_Framework_TestCase
{
    public $curl;

    protected function setUp()
    {
        parent::setUp();

        $this->curl = new CUrl(3);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $url = 'http://demo.phalapi.net/';
        $timeoutMs = 1000;

        $rs = $this->curl->get($url, $timeoutMs);
        //var_dump($rs);

        $this->assertTrue(is_string($rs));

    }

    /**
     * @group testPost
     */ 
    public function testPost()
    {
        $url = 'http://demo.phalapi.net/';
        $data = array('username' => 'phalapi');
        $timeoutMs = 1000;

        $rs = $this->curl->post($url, $data, $timeoutMs);

        $this->assertTrue(is_string($rs));

    }

    public function testSet()
    {
        $this->curl->setHeader(array('Content-Type' => 'text'));
        $this->curl->setOption(array('1' => 'a'));
    }

    public function testCookie()
    {
        $this->curl->setCookie(array('pgv_pvi' => 9739177984, 'username' => 'dogstar'));
        $this->assertNotNull($this->curl->getCookie());

        $this->curl->withCookies();

        $rs = $this->curl->get('http://demo.phalapi.net/', 1000);
    }

    public function testGetRetCookie()
    {
        $cookies = array("a\tb\tc\td\te\tf\tg");
        $mock = new CUrlInnerMock();
        $rs = $mock->getRetCookie($cookies);
        $this->assertEquals(array('f' => 'g'), $rs);
    }

    /**
     * @expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testGetFail()
    {
        $this->curl->get('http_wrong', 100);
    }
}

class CUrlInnerMock extends CUrl {

    public function getRetCookie(array $cookies){
        return parent::getRetCookie($cookies);
    }
}

