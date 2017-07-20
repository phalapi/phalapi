<?php
namespace PhalApi\Tests;

use PhalApi\Helper\Tracer;

/**
 * PhpUnderControl_PhalApiHelperTracer_Test
 *
 * 针对 ../PhalApi/Helper/Tracer.php PhalApi_Helper_Tracer 类的PHPUnit单元测试
 *
 * @author: dogstar 20170415
 */

class PhpUnderControl_PhalApiHelperTracer_Test extends \PHPUnit_Framework_TestCase
{
    public $tracer;

    protected function setUp()
    {
        parent::setUp();

        $this->tracer = new Tracer();
    }

    protected function tearDown()
    {
        \PhalApi\DI()->debug = true;
    }


    /**
     * @group testMark
     */ 
    public function testMark()
    {
        $tag = '';

        $this->tracer->mark($tag);
        $this->tracer->mark('aHa~');
    }

    /**
     * @group testGetReport
     */ 
    public function testGetReport()
    {
        $rs = $this->tracer->getStack();

        $this->assertTrue(is_array($rs));
    }

    public function testMixed()
    {
        $this->tracer->mark('aHa~');
        $this->tracer->mark('BIU~');
        $this->tracer->mark('BlaBla~');

        doSthForTrace($this->tracer);

        $report = $this->tracer->getStack();
        //var_dump($report);
        $this->assertCount(4, $report);
    }

    public function testNoDebug()
    {
        \PhalApi\DI()->debug = false;

        $tracer = new Tracer();
        $tracer->mark('aHa~');

        $report = $tracer->getStack();
        $this->assertCount(0, $report);
    }

    public function testSql()
    {
        $this->tracer->sql('SELECT');
        $this->tracer->sql('DELETE');

        $this->assertCount(2, $this->tracer->getSqls());
    }
}

function doSthForTrace($tracer) {
    $tracer->mark('IN_FUNCTION');
}
