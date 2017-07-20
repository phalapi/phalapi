<?php
namespace PhalApi\Tests;

use PhalApi\Helper\TestRunner;

include_once dirname(__FILE__) . '/runner.php';

/**
 * PhpUnderControl_PhalApiHelperTestRunner_Test
 *
 * 针对 ../PhalApi/Helper/TestRunner.php PhalApi_Helper_TestRunner 类的PHPUnit单元测试
 *
 * @author: dogstar 20170415
 */

class PhpUnderControl_PhalApiHelperTestRunner_Test extends \PHPUnit_Framework_TestCase
{
    public $testRunner;

    protected function setUp()
    {
        parent::setUp();

        $this->testRunner = new TestRunner();
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
            'service' => 'Tests.InnerRunner.Go',
        );

        $rs = TestRunner::go($url, $params);

        $this->assertTrue(is_array($rs));
    }

    /**
     * @expectedException \PhalApi\Exception
     */
    public function testGoWrong()
    {
        TestRunner::go('', array());
    }
}

