<?php
/**
 * PhpUnderControl_CoreConfigFile_Test
 *
 * 针对 ../Core/Config/File.php Core_Config_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('Core_Config_File')) {
    require dirname(__FILE__) . '/../Core/Config/File.php';
}

class PhpUnderControl_CoreConfigFile_Test extends PHPUnit_Framework_TestCase
{
    public $coreConfigFile;

    protected function setUp()
    {
        parent::setUp();

        $this->coreConfigFile = Core_DI::one()->config;
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGetDefault()
    {
        $key = 'sys.noThisKey';
        $default = 2014;

        $rs = $this->coreConfigFile->get($key, $default);

        $this->assertSame($default, $rs);
    }

    public function testGetNormal()
    {
        $key = 'sys.debug';

        $rs = $this->coreConfigFile->get($key);

        $this->assertFalse($rs);
    }

    public function testGetAll()
    {
        $key = 'dbs';

        $rs = $this->coreConfigFile->get($key);

        $this->assertTrue(is_array($rs));
    }
}
