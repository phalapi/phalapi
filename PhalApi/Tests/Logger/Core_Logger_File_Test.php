<?php
/**
 * PhpUnderControl_CoreLoggerFile_Test
 *
 * 针对 ../../Core/Logger/File.php Core_Logger_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20141217
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Core_Logger_File')) {
    require dirname(__FILE__) . '/../../Core/Logger/File.php';
}

class PhpUnderControl_CoreLoggerFile_Test extends PHPUnit_Framework_TestCase
{
    public $coreLoggerFile;

    protected function setUp()
    {
        parent::setUp();

        $this->coreLoggerFile = new Core_Logger_File(dirname(__FILE__) . '/Runtime',
            Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testLog
     */ 
    public function testLog()
    {
        $this->coreLoggerFile->log('debug', 'debug from log', '');
        $this->coreLoggerFile->log('task', 'something test for task', array('from' => 'phpunit'));
    }

    public function testDebug()
    {
        $this->coreLoggerFile->debug('something debug here', array('name' => 'phpunit'));

        $this->coreLoggerFile->debug("This 
            should not be 
            multi line");
    }

    public function testInfo()
    {
        $this->coreLoggerFile->info('something info here', 'phpunit');
        $this->coreLoggerFile->info('something info here', 2014);
        $this->coreLoggerFile->info('something info here', true);
    }

    public function testError()
    {
        $this->coreLoggerFile->error('WTF!');
    }
}
