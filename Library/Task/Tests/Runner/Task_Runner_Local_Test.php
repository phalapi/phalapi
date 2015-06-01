<?php
/**
 * PhpUnderControl_TaskRunnerLocal_Test
 *
 * 针对 ../../Runner/Local.php Task_Runner_Local 类的PHPUnit单元测试
 *
 * @author: dogstar 20150516
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Task_Runner_Local')) {
    require dirname(__FILE__) . '/../../Runner/Local.php';
}

class PhpUnderControl_TaskRunnerLocal_Test extends PHPUnit_Framework_TestCase
{
    public $taskRunnerLocal;

    public $mq;

    protected function setUp()
    {
        parent::setUp();

        $this->mq = new Task_MQ_Array();

        $this->taskRunnerLocal = new Task_Runner_Local($this->mq);
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

class Api_TestTaskDemo extends PhalApi_Api {

    public function update1() {
        return array('code' => 0);
    }

    public function update2() {
        throw new PhalApi_Exception_InternalServerError('just for test');
    }
}
