<?php
/**
 * PhpUnderControl_PhalApiApi_Test
 *
 * 针对 ../PhalApi/Api.php PhalApi_Api 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Api')) {
    require dirname(__FILE__) . '/../PhalApi/Api.php';
}

class PhpUnderControl_PhalApiApi_Test extends PHPUnit_Framework_TestCase
{
    public $coreApi;

    protected function setUp()
    {
        parent::setUp();

        $this->coreApi = new PhalApi_Api();
    }

    protected function tearDown()
    {
        DI()->filter = NULL;
    }

    /**
     * @group testInitialize
     */ 
    public function testInitialize()
    {
        DI()->request = new PhalApi_Request(array('service' => 'Default.Index'));
        $rs = $this->coreApi->init();
    }


    public function testInitializeWithWrongSign()
    {
        $data = array();
        $data['service'] = 'Default.Index';

        DI()->request = new PhalApi_Request($data);
        $rs = $this->coreApi->init();
    }

    public function testInitializeWithRightSign()
    {
        $data = array();
        $data['service'] = 'Default.Index';

        DI()->request = new PhalApi_Request($data);
        $rs = $this->coreApi->init();

    }

    public function testSetterAndGetter()
    {
        $this->coreApi->username = 'phalapi';
        $this->assertEquals('phalapi', $this->coreApi->username);
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testGetUndefinedProperty()
    {
        $this->coreApi->name = 'PhalApi';
        $rs = $this->coreApi->noThisKey;
    }

    public function testApiImpl()
    {
        $data = array();
        $data['service'] = 'Impl.Add';
        $data['version'] = '1.1.0';
        $data['left'] = '6';
        $data['right'] = '1';

        DI()->request = new PhalApi_Request($data);
        DI()->filter = 'PhalApi_Filter_Impl';

        $impl = new PhalApi_Api_Impl();
        $impl->init();

        $rs = $impl->add();
        $this->assertEquals(7, $rs);
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testIllegalFilter()
    {
        DI()->filter = 'PhalApi_Filter_Impl_NotFound';

        $impl = new PhalApi_Api_Impl();
        $impl->init();
    }
}
