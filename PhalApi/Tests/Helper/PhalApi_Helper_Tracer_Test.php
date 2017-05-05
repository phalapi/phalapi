<?php
/**
 * PhpUnderControl_PhalApiHelperTracer_Test
 *
 * 针对 ../PhalApi/Helper/Tracer.php PhalApi_Helper_Tracer 类的PHPUnit单元测试
 *
 * @author: dogstar 20170415
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Helper_Tracer')) {
    require dirname(__FILE__) . '/../PhalApi/Helper/Tracer.php';
}

class PhpUnderControl_PhalApiHelperTracer_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiHelperTracer;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiHelperTracer = new PhalApi_Helper_Tracer();
    }

    protected function tearDown()
    {
        DI()->debug = true;
    }


    /**
     * @group testMark
     */ 
    public function testMark()
    {
        $tag = '';

        $this->phalApiHelperTracer->mark($tag);
        $this->phalApiHelperTracer->mark('aHa~');
    }

    /**
     * @group testGetReport
     */ 
    public function testGetReport()
    {
        $rs = $this->phalApiHelperTracer->getStack();

        $this->assertTrue(is_array($rs));
    }

    public function testMixed()
    {
        $this->phalApiHelperTracer->mark('aHa~');
        $this->phalApiHelperTracer->mark('BIU~');
        $this->phalApiHelperTracer->mark('BlaBla~');

        doSthForTrace($this->phalApiHelperTracer);

        $report = $this->phalApiHelperTracer->getStack();
        //var_dump($report);
        $this->assertCount(4, $report);
    }

    public function testNoDebug()
    {
        DI()->debug = false;

        $tracer = new PhalApi_Helper_Tracer();
        $tracer->mark('aHa~');

        $report = $tracer->getStack();
        $this->assertCount(0, $report);
    }

    public function testSql()
    {
        $this->phalApiHelperTracer->sql('SELECT');
        $this->phalApiHelperTracer->sql('DELETE');

        $this->assertCount(2, $this->phalApiHelperTracer->getSqls());
    }
}

function doSthForTrace($tracer) {
    $tracer->mark('IN_FUNCTION');
}
