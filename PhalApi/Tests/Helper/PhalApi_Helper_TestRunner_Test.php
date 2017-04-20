<?php
/**
 * PhpUnderControl_PhalApiHelperTestRunner_Test
 *
 * 针对 ../PhalApi/Helper/TestRunner.php PhalApi_Helper_TestRunner 类的PHPUnit单元测试
 *
 * @author: dogstar 20170415
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Helper_TestRunner')) {
    require dirname(__FILE__) . '/../../PhalApi/Helper/TestRunner.php';
}

class PhpUnderControl_PhalApiHelperTestRunner_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiHelperTestRunner;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiHelperTestRunner = new PhalApi_Helper_TestRunner();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGo
     */ 
    public function testGo()
    {
        $url = 'demo.phalapi.net';
        $params = array (
            'service' => 'InnerRunner.Go',
        );

        $rs = PhalApi_Helper_TestRunner::go($url, $params);

        $this->assertTrue(is_array($rs));
    }

    /**
     * @expectedException PhalApi_Exception
     */
    public function testGoWrong()
    {
        PhalApi_Helper_TestRunner::go('', array());
    }
}

class APi_InnerRunner extends PhalApi_Api {

    public function go() {
        return array('home');
    }
}
