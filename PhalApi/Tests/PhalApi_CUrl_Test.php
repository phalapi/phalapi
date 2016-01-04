#!/usr/bin/env php
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
        $url = 'http://phalapi.oschina.mopaas.com/Public/demo/';
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
        $url = 'http://phalapi.oschina.mopaas.com/Public/demo/';
        $data = array('username' => 'phalapi');
        $timeoutMs = 1000;

        $rs = $this->phalApiCUrl->post($url, $data, $timeoutMs);

        $this->assertTrue(is_string($rs));

    }

}
