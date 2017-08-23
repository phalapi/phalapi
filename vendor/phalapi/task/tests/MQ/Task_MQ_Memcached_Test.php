<?php
/**
 * PhpUnderControl_TaskMQMemcached_Test
 *
 * 针对 ../../MQ/Memcached.php Task_MQ_Memcached 类的PHPUnit单元测试
 *
 * @author: dogstar 20160430
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

class PhpUnderControl_TaskMQMemcached_Test extends PHPUnit_Framework_TestCase
{
    public $taskMQMemcached;

    protected function setUp()
    {
        parent::setUp();

        $this->taskMQMemcached = new PhalApi\Task\MQ\MemcachedMQ();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $service = 'Task_MQ_Memcached_Default.Index';
        $params = array (
            'username' => 'dogstar',
        );

        $rs = $this->taskMQMemcached->add($service, $params);
        $rs = $this->taskMQMemcached->add($service, $params);
        $rs = $this->taskMQMemcached->add($service, $params);
    }

    /**
     * @group testPop
     */ 
    public function testPop()
    {
        $service = 'Task_MQ_Memcached_Default.Index';
        $num = 2;

        $rs = $this->taskMQMemcached->pop($service, $num);
        $this->assertCount($num, $rs);
    }

}
