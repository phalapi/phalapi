<?php
/**
 * PhpUnderControl_PhalApiFilterSimpleMD5_Test
 *
 * 针对 ../../PhalApi/Filter/SimpleMD5.php PhalApi_Filter_SimpleMD5 类的PHPUnit单元测试
 *
 * @author: dogstar 20151023
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Filter_SimpleMD5')) {
    require dirname(__FILE__) . '/../../PhalApi/Filter/SimpleMD5.php';
}

class PhpUnderControl_PhalApiFilterSimpleMD5_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiFilterSimpleMD5;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiFilterSimpleMD5 = new PhalApi_Filter_SimpleMD5();
        DI()->filter = 'PhalApi_Filter_SimpleMD5';
    }

    protected function tearDown()
    {
        DI()->filter = NULL;
    }


    /**
     * @group testCheck
     */ 
    public function testCheck()
    {
        $rs = $this->phalApiFilterSimpleMD5->check();
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testCheckException()
    {
        $data = array(
            'service' => 'PhalApi_Api_Impl.Add',
            'left' => 1,
            'right' => 1,
        );
        DI()->request = new PhalApi_Request($data);

        $api = new PhalApi_Api_Impl();
        $api->init();
    }

    public function testCheckWithRightSign()
    {
        $data = array(
            'service' => 'PhalApi_Api_Impl.Add',
            'left' => 1,
            'right' => 1,
            'sign' => 'd5c2ea888a6390de5210b9496a1b787a',
        );
        DI()->request = new PhalApi_Request($data);

        $api = new PhalApi_Api_Impl();
        $api->init();
        $rs = $api->add();

        $this->assertEquals(2, $rs);
    }
}
