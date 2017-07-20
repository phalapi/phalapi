<?php
namespace PhalApi\Tests;

use PhalApi\Logger\ExplorerLogger;
use PhalApi\Logger;

/**
 * PhpUnderControl_PhalApiLoggerExplorer_Test
 *
 * 针对 ../test_file_for_loader.php PhalApi_Logger_Explorer 类的PHPUnit单元测试
 *
 * @author: dogstar 20150205
 */

class PhpUnderControl_PhalApiLoggerExplorer_Test extends \PHPUnit_Framework_TestCase
{
    public $explorerLogger;

    protected function setUp()
    {
        parent::setUp();

        $this->explorerLogger = new ExplorerLogger(
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
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

        $this->explorerLogger->log($type, $msg, $data);

        $this->expectOutputRegex('/TEST|this is a test msg|{"from":"testLog"}/');
    }

    public function testLogButNoShow()
    {
        $logger = new ExplorerLogger(0);

        $logger->info('no info');
        $logger->debug('no debug');
        $logger->error('no error');

        $this->expectOutputString('');
    }
}
