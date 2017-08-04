<?php
namespace PhalApi\Tests;

use PhalApi\Helper\ApiDesc;
use PhalApi\Request;
use PhalApi\Api;

include_once dirname(__FILE__) . '/../app.php';

/**
 * PhpUnderControl_PhalApiHelperApiDesc_Test
 *
 * 针对 ../../PhalApi/Helper/ApiDesc.php PhalApi_Helper_ApiDesc 类的PHPUnit单元测试
 *
 * @author: dogstar 20150530
 */

class PhpUnderControl_PhalApiHelperApiDesc_Test extends \PHPUnit_Framework_TestCase
{
    public $apiDesc;

    protected function setUp()
    {
        parent::setUp();

        $this->apiDesc = new ApiDesc('PhalApi Test');
    }

    protected function tearDown()
    {
    }


    /**
     * @group testRender
     */ 
    public function testRenderDefault()
    {
        \PhalApi\DI()->request = new Request(array());
        $rs = $this->apiDesc->render();

        $this->expectOutputRegex("/Site.Index/");
    }

    public function testRenderError()
    {
        \PhalApi\DI()->request = new Request(array('service' => 'NoThisClass.NoThisMethod'));
        $rs = $this->apiDesc->render();

        $this->expectOutputRegex("/NoThisClass.NoThisMethod/");
    }

    public function testRenderNormal()
    {
        \PhalApi\DI()->request = new Request(array('service' => 'UserMock.GetBaseInfo'));
        $rs = $this->apiDesc->render();

        $this->expectOutputRegex("/UserMock.GetBaseInfo/");
    }

    public function testRenderSite()
    {
        \PhalApi\DI()->request = new Request(array('service' => 'Site.Index'));
        $rs = $this->apiDesc->render();

        $this->expectOutputRegex("/Site.Index/");
    }
}

class UserMock extends Api {

    /**
     * @param int user_id ID
     * @return int code sth...
     */
    public function getBaseInfo() {
    }
}
