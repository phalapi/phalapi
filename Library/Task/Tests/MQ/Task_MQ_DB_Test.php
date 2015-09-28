<?php
/**
 * PhpUnderControl_TaskMQDB_Test
 *
 * 针对 ../../MQ/DB.php Task_MQ_DB 类的PHPUnit单元测试
 *
 * @author: dogstar 20150516
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Task_MQ_DB')) {
    require dirname(__FILE__) . '/../../MQ/DB.php';
}

class PhpUnderControl_TaskMQDB_Test extends PHPUnit_Framework_TestCase
{
    public $taskMQDB;

    protected function setUp()
    {
        parent::setUp();

        $this->taskMQDB = new Task_MQ_DB();
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

        $rs = $this->taskMQDB->add($service, $params);

        $this->assertTrue($rs);
    }

    /**
     * @group testPop
     */ 
    public function testPop()
    {
        $this->assertEmpty($this->taskMQDB->pop('NoThisServcie.Index'));

        $service = 'Demo.Update';
        $num = '1';
        $rs = $this->taskMQDB->add($service, array('name' => 'phalapi'));
        $rs = $this->taskMQDB->add($service, array('name' => 'net'));
        $rs = $this->taskMQDB->add($service, array('name' => 'net'));
        $rs = $this->taskMQDB->add($service, array('name' => 'net'));

        $rs = $this->taskMQDB->pop($service, 1);
        $this->assertEquals(array(array('name' => 'phalapi')), $rs);
        $rs = $this->taskMQDB->pop($service, 2);
        $this->assertEquals(array(array('name' => 'net'), array('name' => 'net')), $rs);

        $rs = $this->taskMQDB->pop($service, 10);
        $this->assertCount(1, $rs);
    }

}
