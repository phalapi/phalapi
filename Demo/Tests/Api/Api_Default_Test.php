<?php
/**
 * PhpUnderControl_ApiDefault_Test
 *
 * 针对 ../../Api/Default.php Api_Default 类的PHPUnit单元测试
 *
 * @author: dogstar 20150201
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Api_Default')) {
    require dirname(__FILE__) . '/../../Api/Default.php';
}

class PhpUnderControl_ApiDefault_Test extends PHPUnit_Framework_TestCase
{
    public $apiDefault;

    protected function setUp()
    {
        parent::setUp();

        $this->apiDefault = new Api_Default();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetRules
     */ 
    public function testGetRules()
    {
        $rs = $this->apiDefault->getRules();

        $this->assertNotEmpty($rs);
    }

    public function testIndex()
    {
        //Step 1. 构建请求URL
        $url = 'service=Default.Index&username=dogstar';

        //Step 2. 执行请求	
        $rs = PhalApi_Helper_TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('title', $rs);
        $this->assertArrayHasKey('content', $rs);
        $this->assertArrayHasKey('version', $rs);
        $this->assertArrayHasKey('time', $rs);

        $this->assertEquals('dogstar您好，欢迎使用PhalApi！', $rs['content']);
    }
}
