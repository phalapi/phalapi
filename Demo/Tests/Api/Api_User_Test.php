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
        //当。。。
        $str = 'service=User.GetBaseInfo&user_id=1';

        parse_str($str, $params);
        DI()->request = new PhalApi_Request($params);

        $api = new Api_User(); 
        $api->init();

        //做。。。
        $rs = $api->getBaseInfo();

        //应该。。。
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
        $str = 'service=User.GetMultiBaseInfo&user_ids=1,2,3';
        parse_str($str, $params);

        DI()->request = new PhalApi_Request($params);

        $api = new Api_User();
        //自己进行初始化
        $api->init();
        $rs = $api->getMultiBaseInfo();

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
