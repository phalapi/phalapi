<?php
namespace PhalApi\Tests;

use PhalApi\Config\FileConfig;

/**
 * PhpUnderControl_PhalApiConfigFile_Test
 *
 * 针对 ../PhalApi/Config/File.php PhalApi_Config_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

class PhpUnderControl_PhalApiConfigFile_Test extends \PHPUnit_Framework_TestCase
{
    public $fileConfig;

    protected function setUp()
    {
        parent::setUp();

        $this->fileConfig = new FileConfig(dirname(__FILE__) . '/../../config');
    }

    protected function tearDown()
    {
    }

    public function testConstruct()
    {
    }

    /**
     * @group testGet
     */ 
    public function testGetDefault()
    {
        $key = 'sys.noThisKey';
        $default = 2014;

        $rs = $this->fileConfig->get($key, $default);

        $this->assertSame($default, $rs);
    }

    public function testGetNormal()
    {
        $key = 'sys.debug';

        $rs = $this->fileConfig->get($key);

        $this->assertFalse($rs);
    }

    public function testGetAll()
    {
        $key = 'dbs';

        $rs = $this->fileConfig->get($key);

        $this->assertTrue(is_array($rs));
    }
}
