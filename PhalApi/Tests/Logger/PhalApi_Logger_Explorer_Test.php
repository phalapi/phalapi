<?php
/**
 * PhpUnderControl_PhalApiLoggerExplorer_Test
 *
 * 针对 ../test_file_for_loader.php PhalApi_Logger_Explorer 类的PHPUnit单元测试
 *
 * @author: dogstar 20150205
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Logger_Explorer')) {
    require dirname(__FILE__) . '/../test_file_for_loader.php';
}

class PhpUnderControl_PhalApiLoggerExplorer_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiLoggerExplorer;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiLoggerExplorer = new PhalApi_Logger_Explorer(
            PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testLog
     */ 
    public function testLog()
    {
        $type = 'test';
        $msg = 'this is a test msg';
        $data = array('from' => 'testLog');

        $this->phalApiLoggerExplorer->log($type, $msg, $data);

        $this->expectOutputRegex('/TEST|this is a test msg|{"from":"testLog"}/');
    }

    public function testLogButNoShow()
    {
        $logger = new PhalApi_Logger_Explorer(0);

        $logger->info('no info');
        $logger->debug('no debug');
        $logger->error('no error');

        $this->expectOutputString('');
    }
}
