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
    }

    /**
     * @group testInitialize
     */ 
    public function testInitialize()
    {
        DI()->request = new PhalApi_Request(array('service' => 'Default.Index'));
        $rs = $this->coreApi->initialize();
    }


    public function testInitializeWithWrongSign()
    {
        $data = array();
        $data['service'] = 'Default.Index';

        DI()->request = new PhalApi_Request($data);
        $rs = $this->coreApi->initialize();
    }

    public function testInitializeWithRightSign()
    {
        $data = array();
        $data['service'] = 'Default.Index';

        DI()->request = new PhalApi_Request($data);
        $rs = $this->coreApi->initialize();

    }
}
