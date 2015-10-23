<?php
/**
 * PhpUnderControl_PhalApiFilterNone_Test
 *
 * 针对 ../../PhalApi/Filter/None.php PhalApi_Filter_None 类的PHPUnit单元测试
 *
 * @author: dogstar 20151023
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Filter_None')) {
    require dirname(__FILE__) . '/../../PhalApi/Filter/None.php';
}

class PhpUnderControl_PhalApiFilterNone_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiFilterNone;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiFilterNone = new PhalApi_Filter_None();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testCheck
     */ 
    public function testCheck()
    {
        $rs = $this->phalApiFilterNone->check();
    }

    /**
     * @expectedException Exception
     */
    public function testApiWithCheckException()
    {
        DI()->filter = 'PhalApi_Filter_AlwaysException';
        $api = new Api_Filter_AlwaysException();
        $api->init();
    }
}


class Api_Filter_AlwaysException extends PhalApi_Api
{
    public function go()
    {
        return 'go to BeiJing';
    }
}

class PhalApi_Filter_AlwaysException implements PhalApi_Filter
{
    public function check()
    {
        throw new Exception('just for test');
    }
}
