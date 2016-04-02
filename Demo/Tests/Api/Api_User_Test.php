<?php
/**
 * PhpUnderControl_ApiUser_Test
 *
 * 针对 ../../Demo/Api/User.php Api_User 类的PHPUnit单元测试
 *
 * @author: dogstar 20150128
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Api_User')) {
    require dirname(__FILE__) . '/../../Demo/Api/User.php';
}

class PhpUnderControl_ApiUser_Test extends PHPUnit_Framework_TestCase
{
    public $apiUser;

    protected function setUp()
    {
        parent::setUp();

        $this->apiUser = new Api_User();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetRules
     */ 
    public function testGetRules()
    {
        $rs = $this->apiUser->getRules();
    }

    /**
     * @group testGetBaseInfo
     */ 
    public function testGetBaseInfo()
    {
        //Step 1. 构建请求URL
        $url = 'service=User.GetBaseInfo&user_id=1';

        //Step 2. 执行请求	
        $rs = PhalApi_Helper_TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('info', $rs);

        $this->assertEquals(0, $rs['code']);

        $this->assertEquals('dogstar', $rs['info']['name']);
        $this->assertEquals('oschina', $rs['info']['note']);
    }

    public function testGetMultiBaseInfo()
    {
        //Step 1. 构建请求URL
        $url = 'service=User.GetMultiBaseInfo&user_ids=1,2,3';

        //Step 2. 执行请求	
        $rs = PhalApi_Helper_TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('list', $rs);

        foreach ($rs['list'] as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('note', $item);
        }
    }

}
