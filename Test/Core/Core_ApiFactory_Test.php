<?php
/**
 * PhpUnderControl_CoreApiFactory_Test
 *
 * 针对 ../../Core/ApiFactory.php Core_ApiFactory 类的PHPUnit单元测试
 *
 * @author: dogstar 20141002
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Core_ApiFactory')) {
    require dirname(__FILE__) . '/../../Core/ApiFactory.php';
}

class PhpUnderControl_CoreApiFactory_Test extends PHPUnit_Framework_TestCase
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
        $rs = Core_ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('Core_Api', $rs);
    }

    public function testGenerateNormalClientService()
    {
        $data['service'] = 'Default.Index';
        $data['sign'] = '1ec92737c7c287c7249e0adef566544a';

        Core_DI::one()->request = new Core_Request($data);
        $rs = Core_ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('Core_Api', $rs);
        $this->assertInstanceOf('Api_Default', $rs);
    }

    /**
     * @expectedException Core_Exception_BadRequest
     */
    public function testGenerateIllegalApiService()
    {
        $data['service'] = 'NoThisService.index';
        Core_DI::one()->request = new Core_Request($data);
        $rs = Core_ApiFactory::generateService();
    }

    /**
     * @expectedException Core_Exception_BadRequest
     */
    public function testGenerateIllegalActionService()
    {
        $data['service'] = 'Default.noThisFunction';
        Core_DI::one()->request = new Core_Request($data);
        $rs = Core_ApiFactory::generateService();
    }

}
