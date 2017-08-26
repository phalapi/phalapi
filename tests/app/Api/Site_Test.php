<?php
namespace App;

use App\Api\Site;
use PhalApi\Helper\TestRunner;

/**
 * PhpUnderControl_ApiSite_Test
 *
 * 针对 App\Api\Site 类的PHPUnit单元测试
 *
 * @author: dogstar 20170703
 */

class PhpUnderControl_ApiSite_Test extends \PHPUnit_Framework_TestCase
{
    public $site;

    protected function setUp()
    {
        parent::setUp();

        $this->site = new Site();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetRules
     */ 
    public function testGetRules()
    {
        $rs = $this->site->getRules();

        $this->assertNotEmpty($rs);
    }

    public function testIndex()
    {
        //Step 1. 构建请求URL
        $url = 'service=App.Site.Index&username=dogstar';

        //Step 2. 执行请求	
        $rs = TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('title', $rs);
    }
}
