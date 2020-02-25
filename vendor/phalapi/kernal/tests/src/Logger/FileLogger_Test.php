<?php
namespace PhalApi\Tests;

use PhalApi\Logger;
use PhalApi\Logger\FileLogger;

/**
 * PhpUnderControl_PhalApiLoggerFile_Test
 *
 * 针对 ../../PhalApi/Logger/File.php PhalApi_Logger_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20141217
 */

class PhpUnderControl_PhalApiLoggerFile_Test extends \PHPUnit_Framework_TestCase
{
    public $coreLoggerFile;

    protected function setUp()
    {
        parent::setUp();

        $cmd = sprintf('rm %s -rf', dirname(__FILE__) . '/runtime');
        shell_exec($cmd);

        $this->coreLoggerFile = new FileLogger(dirname(__FILE__) . '/runtime',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
    }

    protected function tearDown()
    {
        $cmd = sprintf('rm %s -rf', dirname(__FILE__) . '/runtime');
        shell_exec($cmd);
    }


    /**
     * @group testLog
     */ 
    public function testLog()
    {
        $this->coreLoggerFile->log('debug', 'debug from log', '');
        $this->coreLoggerFile->log('task', 'something test for task', array('from' => 'phpunit'));

        $this->assertLogExists('debug from log');
        $this->assertLogExists('something test for task');
    }

    public function testDebug()
    {
        $this->coreLoggerFile->debug('something debug here', array('name' => 'phpunit'));

        $this->coreLoggerFile->debug("This 
            should not be 
            multi line");

        $this->assertLogExists('something debug here');
        $this->assertLogExists('This');
        $this->assertLogExists('should not be');
        $this->assertLogExists('multi');
    }

    public function testInfo()
    {
        $this->coreLoggerFile->info('something info here', 'phpunit');
        $this->coreLoggerFile->info('something info here', 2014);
        $this->coreLoggerFile->info('something info here', true);
        $this->coreLoggerFile->info('这是一段中文');

        $this->assertLogExists('something info here');
        $this->assertLogExists('phpunit');
        $this->assertLogExists('2014');
        $this->assertLogExists('1');
        $this->assertLogExists('这是一段中文');
    }

    public function testError()
    {
        $this->coreLoggerFile->error('WTF!');

        $this->assertLogExists('WTF!');
    }

    protected function assertLogExists($content)
    {
        $logFile = implode(DIRECTORY_SEPARATOR, array(
            dirname(__FILE__) . '/runtime',
            'log',
            date('Ym', time()),
            date('Ymd', time()) . '.log'
        ));

        $this->assertContains($content, file_get_contents($logFile));
    }

    /**
     * @  expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testPermissionDenied()
    {
        $coreLoggerFile = new FileLogger('/var/never_this',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d H:i:s', false);
    }

    /**
     * @  expectedException PhalApi\Exception\InternalServerErrorException
     */
    public function testPermissionDeniedWhenLog()
    {
        $coreLoggerFile = new FileLogger('/var/never_this',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d H:i:s', false);
        $coreLoggerFile->info('here we go to fail');
    }

    public function testPermissionDeniedSlice()
    {
        $coreLoggerFile = new FileLogger('/var/never_this',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d', false);
    }

    public function testPermissionDeniedWhenLogSlice()
    {
        $coreLoggerFile = new FileLogger('/var/never_this',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d', false);
        $coreLoggerFile->info('here we go to fail');
    }

    public function testLogFilePrefixx()
    {
        $coreLoggerFile = new FileLogger(dirname(__FILE__) . '/runtime',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d', false, 'phpunit');
        $coreLoggerFile->info('here we go to at phpunit_* log file');
    }

    public function testSwithLogFilePrefixx()
    {
        $coreLoggerFile = new FileLogger(dirname(__FILE__) . '/runtime',
            Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR, 'Y-m-d', false, 'phpunit');
        $coreLoggerFile->info('here we go to at phpunit_* log file');

        $coreLoggerFile->switchFilePrefix('dogstar')->info('这将会保存在dogstar前缀的日记文件中');

        $coreLoggerFile->info('还是在dogstar前缀的日记文件');
    }

    public function testCreate() {
        $coreLoggerFile = FileLogger::create(array('log_folder' => dirname(__FILE__) . '/runtime'));
        $coreLoggerFile->info('here we go from 创建函数');
        $this->assertLogExists('创建函数');
    }
}
