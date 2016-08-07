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
        $this->assertLogExists('should not be \n');
        $this->assertLogExists('multi');
    }

    public function testInfo()
    {
        $this->coreLoggerFile->info('something info here', 'phpunit');
        $this->coreLoggerFile->info('something info here', 2014);
        $this->coreLoggerFile->info('something info here', true);

        $this->assertLogExists('something info here');
        $this->assertLogExists('phpunit');
        $this->assertLogExists('2014');
        $this->assertLogExists('1');
    }

    public function testError()
    {
        $this->coreLoggerFile->error('WTF!');

        $this->assertLogExists('WTF!');
    }

    protected function assertLogExists($content)
    {
        $logFile = implode(DIRECTORY_SEPARATOR, array(
            dirname(__FILE__) . '/Runtime',
            'log',
            date('Ym', time()),
            date('Ymd', time()) . '.log'
        ));

        $this->assertContains($content, file_get_contents($logFile));
    }
}
