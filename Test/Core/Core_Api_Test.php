<?php
/**
 * PhpUnderControl_CoreApi_Test
 *
 * 针对 ../../Core/Api.php Core_Api 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Core_Api')) {
    require dirname(__FILE__) . '/../../Core/Api.php';
}

class PhpUnderControl_CoreApi_Test extends PHPUnit_Framework_TestCase
{
    public $coreApi;

    protected function setUp()
    {
        parent::setUp();

        $this->coreApi = new Core_Api();
    }

    protected function tearDown()
    {
    }

    /**
     * @group testInitialize
     */ 
    public function testInitialize()
    {
        Core_DI::one()->request = new Core_Request(array('__debug__' => 1, 'appKey' => 'mini', 'sign' => '', 'service' => 'Default.index'));
        $rs = $this->coreApi->initialize();
    }


    public function testInitializeWithWrongSign()
    {
        $data[] = array();
        $data['service'] = 'Default.index';

        Core_DI::one()->request = new Core_Request($data);
        $rs = $this->coreApi->initialize();
    }

    public function testInitializeWithRightSign()
    {
        $data = array();
        $data['service'] = 'Default.index';

        Core_DI::one()->request = new Core_Request($data);
        $rs = $this->coreApi->initialize();

    }
}
