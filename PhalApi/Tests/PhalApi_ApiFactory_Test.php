<?php
/**
 * PhpUnderControl_PhalApiApiFactory_Test
 *
 * 针对 ../../PhalApi/ApiFactory.php PhalApi_ApiFactory 类的PHPUnit单元测试
 *
 * @author: dogstar 20141002
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_ApiFactory')) {
    require dirname(__FILE__) . '/../PhalApi/ApiFactory.php';
}

class PhpUnderControl_PhalApiApiFactory_Test extends PHPUnit_Framework_TestCase
{
    public $coreApiFactory;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGenerateService
     */ 
    public function testGenerateService()
    {
        $rs = PhalApi_ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('PhalApi_Api', $rs);
    }

    public function testGenerateNormalClientService()
    {
        $data['service'] = 'Default.Index';
        $data['sign'] = '1ec92737c7c287c7249e0adef566544a';

        DI()->request = new PhalApi_Request($data);
        $rs = PhalApi_ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('PhalApi_Api', $rs);
        $this->assertInstanceOf('Api_Default', $rs);
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testGenerateIllegalApiService()
    {
        $data['service'] = 'NoThisService.Index';
        DI()->request = new PhalApi_Request($data);
        $rs = PhalApi_ApiFactory::generateService();
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testGenerateIllegalActionService()
    {
        $data['service'] = 'Default.noThisFunction';
        DI()->request = new PhalApi_Request($data);
        $rs = PhalApi_ApiFactory::generateService();
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testIllegalServiceName()
    {
        $data['service'] = 'Default';
        DI()->request = new PhalApi_Request($data);
        $rs = PhalApi_ApiFactory::generateService();
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testNotPhalApiSubclass()
    {
        $data['service'] = 'Crazy.What';
        DI()->request = new PhalApi_Request($data);
        $rs = PhalApi_ApiFactory::generateService();
    }
}

class Api_Default extends PhalApi_Api
{
    public function index()
    {
    }
}

class Api_Crazy
{
    public function what()
    {
    }
}
