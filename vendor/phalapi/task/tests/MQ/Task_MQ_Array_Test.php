<?php
/**
 * PhpUnderControl_TaskMQArray_Test
 *
 * 针对 ../../MQ/Array.php Task_MQ_Array 类的PHPUnit单元测试
 *
 * @author: dogstar 20150516
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

class PhpUnderControl_TaskMQArray_Test extends PHPUnit_Framework_TestCase
{
    public $taskMQArray;

    protected function setUp()
    {
        parent::setUp();

        $this->taskMQArray = new PhalApi\Task\MQ\ArrayMQ();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $service = 'Demo.Test';
        $params = array (
            'id' => 1,
        );

        $rs = $this->taskMQArray->add($service, $params);

        $this->assertTrue($rs);
    }

    /**
     * @group testPop
     */ 
    public function testPop()
    {
        $service = 'Demo.Update';
        $num = '1';

        $rs = $this->taskMQArray->pop($service, $num);

        $this->assertEmpty($rs);

        $rs = $this->taskMQArray->add($service, array('name' => 'phalapi'));
        $rs = $this->taskMQArray->add($service, array('name' => 'net'));

        $rs = $this->taskMQArray->pop($service, 1);
        $this->assertEquals(array(array('name' => 'phalapi')), $rs);
        $rs = $this->taskMQArray->pop($service, 1);
        $this->assertEquals(array(array('name' => 'net')), $rs);
    }

}
