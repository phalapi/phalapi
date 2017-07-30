<?php
/**
 * PhpUnderControl_PhalApiCUrl_Test
 *
 * 针对 ../PhalApi/CUrl.php PhalApi_CUrl 类的PHPUnit单元测试
 *
 * @author: dogstar 20150415
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_CUrl')) {
    require dirname(__FILE__) . '/../PhalApi/CUrl.php';
}

class PhpUnderControl_PhalApiCUrl_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCUrl;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiCUrl = new PhalApi_CUrl(3);
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

        $rs = $this->phalApiCUrl->get($url, $timeoutMs);
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

        $rs = $this->phalApiCUrl->post($url, $data, $timeoutMs);

        $this->assertTrue(is_string($rs));

    }

    public function testSetHeader()
    {
        $this->phalApiCUrl->setHeader(array('Content-Type' => 'UTF-8'));
    }

    public function testSetOption()
    {
        $this->phalApiCUrl->setOption(array(1 => 300));
    }

    public function testCookie()
    {
        $this->phalApiCUrl->setCookie(array('pgv_pvi' => 9739177984, 'username' => 'dogstar'));
        $this->assertNotNull($this->phalApiCUrl->getCookie());

        $this->phalApiCUrl->withCookies();

        $rs = $this->phalApiCUrl->get('http://demo.phalapi.net/', 1000);
    }

    public function testGetRetCookie()
    {
        $cookies = array("a\tb\tc\td\te\tf\tg");
        $mock = new PhalApi_CUrl_InnerMock();
        $rs = $mock->getRetCookie($cookies);
        $this->assertEquals(array('f' => 'g'), $rs);
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testGetFail()
    {
        $this->phalApiCUrl->get('http_wrong', 100);
    }
}

class PhalApi_CUrl_InnerMock extends PhalApi_CUrl {

    public function getRetCookie(array $cookies){
        return parent::getRetCookie($cookies);
    }
}
