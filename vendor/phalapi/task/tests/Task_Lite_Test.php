<?php
/**
 * PhpUnderControl_TaskLite_Test
 *
 * 针对 ../Lite.php Task_Lite 类的PHPUnit单元测试
 *
 * @author: dogstar 20150514
 */

require_once dirname(__FILE__) . '/bootstrap.php';

class PhpUnderControl_TaskLite_Test extends PHPUnit_Framework_TestCase
{
    public $taskLite;

    protected function setUp()
    {
        parent::setUp();

        $this->taskLite = new PhalApi\Task\Lite(new PhalApi\Task\MQ\FileMQ());
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $service = 'Demo.Update';
        $params = array (
            'id' => 888
        );

        $rs = $this->taskLite->add($service, $params);
        $this->assertTrue($rs);
    }

    public function testAddWrong()
    {
        $service = 'Demo';

        $rs = $this->taskLite->add($service);

        $this->assertFalse($rs);
    }
}
