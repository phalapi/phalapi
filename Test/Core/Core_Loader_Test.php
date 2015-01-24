<?php
/**
 * PhpUnderControl_CoreLoader_Test
 *
 * 针对 ../../Core/Loader.php Core_Loader 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Core_Loader')) {
    require dirname(__FILE__) . '/../../Core/Loader.php';
}

class PhpUnderControl_CoreLoader_Test extends PHPUnit_Framework_TestCase
{
    public $coreLoader;

    protected function setUp()
    {
        parent::setUp();

        $this->coreLoader = Core_DI::one()->loader;
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAddDirs
     */ 
    public function testAddDirs()
    {
        $dirs = array('FirstDir', 'SecondDir');

        $this->coreLoader->addDirs($dirs);
    }

    /**
     * @group testSetBasePath
     */ 
    public function testSetBasePath()
    {
        $path = PHALAPI_ROOT;

        $rs = $this->coreLoader->setBasePath($path);
    }

    /**
     * @group testLoadFile
     */ 
    public function testLoadFile()
    {
        $filePath = PHALAPI_ROOT . '/Test/Core_tests/test_file_for_loader.php';

        $this->coreLoader->loadFile($filePath);
    }

    /**
     * @group testLoad
     */ 
    public function testLoad()
    {
        $className = 'Core_Api';

        $rs = $this->coreLoader->load($className);
    }

    public function testLoadOnce()
    {
        $obj = new Core_Logger_File('./', 0);
    }

    public function testRegisterAgain()
    {
        $loader = new Core_Loader('./', array());
        $loader = new Core_Loader('./', array());

        $obj = new Core_DB_NotORM(array());
    }
}
