<?php
/**
 * PhpUnderControl_ModelUser_Test
 *
 * 针对 ./Demo/Model/User.php Model_User 类的PHPUnit单元测试
 *
 * @author: dogstar 20150208
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Model_User')) {
    require dirname(__FILE__) . '/./Demo/Model/User.php';
}

class PhpUnderControl_ModelUser_Test extends PHPUnit_Framework_TestCase
{
    public $modelUser;

    protected function setUp()
    {
        parent::setUp();

        $this->modelUser = new Model_User();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetByUserId
     */ 
    public function testGetByUserId()
    {
        $userId = '1';

        $rs = $this->modelUser->getByUserId($userId);

        $this->assertArrayHasKey('id', $rs);
        $this->assertArrayHasKey('name', $rs);
        $this->assertArrayHasKey('note', $rs);

        $this->assertEquals('dogstar', $rs['name']);
    }

}
