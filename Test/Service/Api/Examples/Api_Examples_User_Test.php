<?php
/**
 * PhpUnderControl_ApiExamplesUser_Test
 *
 * 针对 ../../../../Service/Api/Examples/User.php Api_Examples_User 类的PHPUnit单元测试
 *
 * @author: dogstar 20150127
 */

require_once dirname(__FILE__) . '/../../../test_env.php';

if (!class_exists('Api_Examples_User')) {
    require dirname(__FILE__) . '/../../../../Service/Api/Examples/User.php';
}

class PhpUnderControl_ApiExamplesUser_Test extends PHPUnit_Framework_TestCase
{
    public $apiExamplesUser;

    protected function setUp()
    {
        parent::setUp();

        $this->apiExamplesUser = new Api_Examples_User();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetBaseInfo
     */ 
    public function testGetBaseInfo()
    {
        $str = 'service=Examples_User.GetBaseInfo&userId=1';
        parse_str($str, $params);

        Core_DI::one()->request = new Core_Request($params);

        $api = new Api_Examples_User(); 
        $api->initialize();
        $rs = $api->getBaseInfo();

        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('info', $rs);

        $this->assertEquals(0, $rs['code']);

        $this->assertEquals('dogstar', $rs['info']['name']);
        $this->assertEquals('oschina', $rs['info']['from']);
    }

}
