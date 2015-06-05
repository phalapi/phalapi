<?php
/**
 * PhpUnderControl_TaskMQRedis_Test
 *
 * 针对 ../../MQ/Redis.php Task_MQ_Redis 类的PHPUnit单元测试
 *
 * @author: Task_MQ_Redis 20150516
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Task_MQ_Redis')) {
    require dirname(__FILE__) . '/../../MQ/Redis.php';
}

class PhpUnderControl_TaskMQRedis_Test extends PHPUnit_Framework_TestCase
{
    public $taskMQRedis;

    protected function setUp()
    {
        parent::setUp();

        $this->taskMQRedis = new Task_MQ_Redis();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $service = 'TaskRedis.Test';
        $params = array (
            'id' => 1,
        );

        $rs = $this->taskMQRedis->add($service, $params);

        $this->taskMQRedis->add($service, array('id' => 2));
    }

    /**
     * @group testPop
     */ 
    public function testPop()
    {
        $service = 'TaskRedis.Test';
        $num = 2;

        $rs = $this->taskMQRedis->pop($service, $num);

        $this->assertEquals(array(array('id' => 1), array('id' => 2)), $rs);
    }

}
