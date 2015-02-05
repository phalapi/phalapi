<?php
/**
 * PhpUnderControl_PhalApiLoggerFile_Test
 *
 * 针对 ../../PhalApi/Logger/File.php PhalApi_Logger_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20141217
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Logger_File')) {
    require dirname(__FILE__) . '/../../PhalApi/Logger/File.php';
}

class PhpUnderControl_PhalApiLoggerFile_Test extends PHPUnit_Framework_TestCase
{
    public $coreLoggerFile;

    protected function setUp()
    {
        parent::setUp();

        $cmd = sprintf('rm %s -rf', dirname(__FILE__) . '/Runtime');
        shell_exec($cmd);

        $this->coreLoggerFile = new PhalApi_Logger_File(dirname(__FILE__) . '/Runtime',
            PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);
    }

    protected function tearDown()
    {
        $cmd = sprintf('rm %s -rf', dirname(__FILE__) . '/Runtime');
        shell_exec($cmd);
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
