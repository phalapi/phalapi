<?php
/**
 * PhpUnderControl_TaskRunner_Test
 *
 * 针对 ../Runner.php Task_Runner 类的PHPUnit单元测试
 *
 * @author: dogstar 20150515
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('Task_Runner')) {
    require dirname(__FILE__) . '/../Runner.php';
}

class PhpUnderControl_TaskRunner_Test extends PHPUnit_Framework_TestCase
{
    protected $fileMq;

    public $taskRunner;

    protected function setUp()
    {
        parent::setUp();

        $this->fileMq = new Task_MQ_File();
        $this->taskRunner = new Task_Runner_Mock($this->fileMq);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGo
     */ 
    public function testGo()
    {
        $service = 'Demo.TestGo';

        $this->fileMq->add($service, array());
        $this->fileMq->add($service, array('id' => 123));
        $this->fileMq->add($service, array('id' => 888, 'name' => 'phalapi'));

        $rs = $this->taskRunner->go($service);
        //var_dump($rs);

        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('total', $rs);
        $this->assertArrayHasKey('fail', $rs);

        $this->assertEquals(3, $rs['total']);
        $this->assertEquals(0, $rs['fail']);
    }

}

class Task_Runner_Mock extends Task_Runner {

    protected function youGo($service, $params) {
        echo "Task_Runner_Mock::youGo(", $service , ", ", json_encode($params), ") ... \n";
        return true;
    }
}
