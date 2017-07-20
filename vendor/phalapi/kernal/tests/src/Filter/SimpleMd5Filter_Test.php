<?php
namespace PhalApi\Tests;

use PhalApi\Filter\SimpleMD5Filter;
use PhalApi\Request;
use PhalApi\Exception\BadRequestException;

/**
 * PhpUnderControl_PhalApiFilterSimpleMD5_Test
 *
 * 针对 ../../PhalApi/Filter/SimpleMD5.php PhalApi_Filter_SimpleMD5 类的PHPUnit单元测试
 *
 * @author: dogstar 20151023
 */

class PhpUnderControl_PhalApiFilterSimpleMD5_Test extends \PHPUnit_Framework_TestCase
{
    public $simpleMD5Filter;

    protected function setUp()
    {
        parent::setUp();

        $this->simpleMD5Filter = new SimpleMD5Filter();
        \PhalApi\DI()->filter = '\\PhalApi\\Filter\\SimpleMD5Filter';
    }

    protected function tearDown()
    {
        \PhalApi\DI()->filter = NULL;
    }


    /**
     * @group testCheck
     */ 
    public function testCheck()
    {
        try {
            $rs = $this->simpleMD5Filter->check();
        } catch (BadRequestException $ex) {
        }
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testCheckException()
    {
        $data = array(
            'service' => 'ImplApi.Add',
            'left' => 1,
            'right' => 1,
        );
        \PhalApi\DI()->request = new Request($data);

        $api = new ImplApi();
        $api->init();
    }

    public function testCheckWithRightSign()
    {
        $data = array(
            'service' => 'ImplApi.Add',
            'left' => 1,
            'right' => 1,
            'sign' => '6d35103bff93178d073b185f5e4c32fa',
        );
        \PhalApi\DI()->request = new Request($data);

        $api = new ImplApi();
        $api->init();
        $rs = $api->add();

        $this->assertEquals(2, $rs);
    }
}
