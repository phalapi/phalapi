<?php
/**
 * PhpUnderControl_TaskMQFile_Test
 *
 * 针对 ../../MQ/File.php Task_MQ_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20160430
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

class PhpUnderControl_TaskMQFile_Test extends PHPUnit_Framework_TestCase
{
    public $taskMQFile;

    protected function setUp()
    {
        parent::setUp();

        $this->taskMQFile = new PhalApi\Task\MQ\FileMQ();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $service = 'Task_MQ_File_Default.Index';
        $params = array (
            'username' => 'dogstar',
        );

        $rs = $this->taskMQFile->add($service, $params);
        $rs = $this->taskMQFile->add($service, $params);
        $rs = $this->taskMQFile->add($service, $params);
    }

    /**
     * @group testPop
     */ 
    public function testPop()
    {
        $service = 'Task_MQ_File_Default.Index';
        $num = 2;

        $rs = $this->taskMQFile->pop($service, $num);
        $this->assertCount($num, $rs);
    }

}
