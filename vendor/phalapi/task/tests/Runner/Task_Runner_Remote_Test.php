<?php
/**
 * PhpUnderControl_TaskRunnerRemote_Test
 *
 * 针对 ../../Runner/Remote.php Task_Runner_Remote 类的PHPUnit单元测试
 *
 * @author: dogstar 20150516
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

class PhpUnderControl_TaskRunnerRemote_Test extends PHPUnit_Framework_TestCase
{
    public $taskRunnerRemote;

    protected $mq;

    protected function setUp()
    {
        parent::setUp();

        $this->mq = new PhalApi\Task\MQ\ArrayMQ();

        $this->taskRunnerRemote = new PhalApi\Task\Runner\RemoteRunner($this->mq);
    }

    protected function tearDown()
    {
    }

    public function testHere()
    {
        $service1 = 'Default.Index';
        $this->mq->add($service1, array('username' => 'phalapi'));
        $this->mq->add($service1, array('username' => 'net'));

        $service2 = 'WrongUser.GetBaseInfo';
        $this->mq->add($service2, array('userId' => 1));

        $rs = $this->taskRunnerRemote->go($service1);
        $this->assertEquals(2, $rs['total']);
        $this->assertEquals(0, $rs['fail']);

        $rs = $this->taskRunnerRemote->go($service2);
        $this->assertEquals(1, $rs['total']);
        $this->assertEquals(1, $rs['fail']);

        $rs = $this->taskRunnerRemote->go('TestTaskDemo.Update');
        $this->assertEquals(0, $rs['total']);
        $this->assertEquals(0, $rs['fail']);
    }

}
