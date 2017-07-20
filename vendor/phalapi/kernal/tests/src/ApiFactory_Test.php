<?php
namespace PhalApi\Tests;

use PhalApi\ApiFactory;
use PhalApi\Exception\BadRequestException;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Request;

include_once dirname(__FILE__) . '/app.php';

/**
 * PhpUnderControl_PhalApiApiFactory_Test
 *
 * 针对 ../../PhalApi/ApiFactory.php ApiFactory 类的PHPUnit单元测试
 *
 * @author: dogstar 20141002
 */

class PhpUnderControl_PhalApiApiFactory_Test extends \PHPUnit_Framework_TestCase
{
    public $coreApiFactory;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        \PhalApi\DI()->filter = NULL;
    }


    /**
     * @group testGenerateService
     */ 
    public function testGenerateService()
    {
        $rs = ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('\\PhalApi\\Api', $rs);
    }

    public function testGenerateNormalClientService()
    {
        $data['service'] = 'Site.Index';
        $data['sign'] = '1ec92737c7c287c7249e0adef566544a';

        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();

        $this->assertNotNull($rs);
        $this->assertInstanceOf('\\PhalApi\\Api', $rs);
        $this->assertInstanceOf('\\App\\Api\\Site', $rs);
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testGenerateIllegalApiService()
    {
        $data['service'] = 'NoThisService.Index';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testGenerateIllegalActionService()
    {
        $data['service'] = 'Default.noThisFunction';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException 
     */
    public function testIllegalServiceName()
    {
        $data['service'] = 'Default';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }

    /**
     * @expectedException \PhalApi\Exception\InternalServerErrorException
     */
    public function testNotPhalApiSubclass()
    {
        $data['service'] = 'Crazy.What';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testServiceWhitelistNOTInclude()
    {
        \PhalApi\DI()->filter = new ImplExceptionFilter();

        $data['service'] = 'ServiceWhitelist.GetTime';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }

    /**
     * @dataProvider provideDataForWhilelist
     */
    public function testServiceWhitelistInclude($service)
    {
        \PhalApi\DI()->filter = new ImplExceptionFilter();

        $data['service'] = $service;
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();

        $this->assertInstanceOf('\\PhalApi\\Api', $rs);
    }

    public function provideDataForWhilelist()
    {
        return array(
            array('App.ServiceWhitelist.Index'),
            array('App.ServiceWhitelist.PoPo'),
            array('App.Site.Index'),
        );
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testWrongAction()
    {
        $data = array();
        $data['service'] = 'ServiceWhitelist.NoThisMethod';
        \PhalApi\DI()->request = new Request($data);
        $rs = ApiFactory::generateService();
    }
}
