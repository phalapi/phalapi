<?php
/**
 * PhpUnderControl_PhalApiLoader_Test
 *
 * 针对 ../PhalApi/Loader.php PhalApi_Loader 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Loader')) {
    require dirname(__FILE__) . '/../PhalApi/Loader.php';
}

class PhpUnderControl_PhalApiLoader_Test extends PHPUnit_Framework_TestCase
{
    public $coreLoader;

    protected function setUp()
    {
        parent::setUp();

        $this->coreLoader = DI()->loader;
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
        $filePath = dirname(__FILE__) . '/test_file_for_loader.php';

        $this->coreLoader->loadFile($filePath);
    }

    /**
     * @group testLoad
     */ 
    public function testLoad()
    {
        $className = 'PhalApi_Api';

        $rs = $this->coreLoader->load($className);
    }

    public function testLoadOnce()
    {
        $obj = new PhalApi_Logger_File('./', 0);
    }

    public function testRegisterAgain()
    {
        $loader = new PhalApi_Loader('./', array());
        $loader = new PhalApi_Loader('./', array());

        $obj = new PhalApi_DB_NotORM(array());
    }

    public function testConstructAndAdd()
    {
        $loader = new PhalApi_Loader('./', array('./Config'));
        $loader->addDirs('./Data');
        $loader->addDirs(array('./Crypt'));
    }
}
