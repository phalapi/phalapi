<?php
/**
 * PhpUnderControl_PhalApiHelperApiDesc_Test
 *
 * 针对 ../../PhalApi/Helper/ApiDesc.php PhalApi_Helper_ApiDesc 类的PHPUnit单元测试
 *
 * @author: dogstar 20150530
 */

require_once dirname(__FILE__) . '/../test_env.php';
DI()->loader->addDirs(dirname(__FILE__) . '/../../../Demo');

if (!class_exists('PhalApi_Helper_ApiDesc')) {
    require dirname(__FILE__) . '/../../PhalApi/Helper/ApiDesc.php';
}

class PhpUnderControl_PhalApiHelperApiDesc_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiHelperApiDesc;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiHelperApiDesc = new PhalApi_Helper_ApiDesc();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testRender
     */ 
    public function testRenderDefault()
    {
        DI()->request = new PhalApi_Request(array());
        $rs = $this->phalApiHelperApiDesc->render();

        $this->expectOutputRegex("/Default.Index/");
    }

    public function testRenderError()
    {
        DI()->request = new PhalApi_Request(array('service' => 'NoThisClass.NoThisMethod'));
        $rs = $this->phalApiHelperApiDesc->render();

        $this->expectOutputRegex("/NoThisClass.NoThisMethod/");
    }

    public function testRenderNormal()
    {
        DI()->request = new PhalApi_Request(array('service' => 'Helper_User_Mock.GetBaseInfo'));
        $rs = $this->phalApiHelperApiDesc->render();

        $this->expectOutputRegex("/Helper_User_Mock.GetBaseInfo/");
    }
}

class Api_Helper_User_Mock extends PhalApi_Api {

    /**
     * @param int user_id ID
     * @return int code sth...
     */
    public function getBaseInfo() {
    }
}
