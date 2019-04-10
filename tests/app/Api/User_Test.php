<?php
namespace App;

use App\Api\User;
use PhalApi\Helper\TestRunner;

/**
 * PhpUnderControl_App\Api\User_Test
 *
 * 针对 ./src/app/Api/User.php App\Api\User 类的PHPUnit单元测试
 *
 * @author: dogstar 20180818
 */
class PhpUnderControl_AppApiUser_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiUser;

    protected function setUp()
    {
        parent::setUp();

        $this->appApiUser = new User();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }


    /**
     * @group testGetRules
     */ 
    public function testGetRules()
    {
        $rs = $this->appApiUser->getRules();
        $this->assertTrue(is_array($rs));
    }

    /**
     * @group testLogin
     */ 
    public function testLogin()
    {
        //Step 1. 构建请求URL
        $url = 'service=App.User.Login&username=dogstar&password=123456';

        //Step 2. 执行请求	
        $rs = TestRunner::go($url);

        //Step 3. 验证
        $this->assertTrue($rs['is_login']);
        $this->assertSame(8, $rs['user_id']);
    }

}
