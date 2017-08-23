<?php
/**
 * PhpUnderControl_TaskRunnerLocal_Test
 *
 * 针对 ../../Runner/Local.php Task_Runner_Local 类的PHPUnit单元测试
 *
 * @author: dogstar 20150516
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

include_once dirname(__FILE__) . '/testtaskdemo.php';

class PhpUnderControl_TaskRunnerLocal_Test extends PHPUnit_Framework_TestCase
{
    public $taskRunnerLocal;

    public $mq;

    protected function setUp()
    {
        parent::setUp();

        $this->mq = new PhalApi\Task\MQ\ArrayMQ();

        $this->taskRunnerLocal = new PhalApi\Task\Runner\LocalRunner($this->mq);
    }

    protected function tearDown()
    {
    }

    public function testHere()
    {
        $service1 = 'TestTaskDemo.Update1';
        $this->mq->add($service1, array('name' => 'phalapi'));
        $this->mq->add($service1, array('name' => 'net'));

        $service2 = 'TestTaskDemo.Update2';
        $this->mq->add($service2, array('id' => 1));

        $rs = $this->taskRunnerLocal->go($service1);
        $this->assertEquals(2, $rs['total']);
        $this->assertEquals(0, $rs['fail']);

        $rs = $this->taskRunnerLocal->go($service2);
        $this->assertEquals(1, $rs['total']);
        $this->assertEquals(1, $rs['fail']);

        $rs = $this->taskRunnerLocal->go('TestTaskDemo.Update3');
        $this->assertEquals(0, $rs['total']);
        $this->assertEquals(0, $rs['fail']);
    }

}

