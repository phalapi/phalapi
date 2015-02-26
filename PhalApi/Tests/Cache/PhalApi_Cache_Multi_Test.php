<?php
/**
 * PhpUnderControl_PhalApiCacheMulti_Test
 *
 * 针对 ../../PhalApi/Cache/Multi.php PhalApi_Cache_Multi 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Cache_Multi')) {
    require dirname(__FILE__) . '/../../PhalApi/Cache/Multi.php';
}

class PhpUnderControl_PhalApiCacheMulti_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCacheMulti;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiCacheMulti = new PhalApi_Cache_Multi();

        $fileCache = new PhalApi_Cache_File(array('path' => dirname(__FILE__)));

        $this->phalApiCacheMulti->addCache($fileCache);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testAddCache
     */ 
    public function testAddCache()
    {
        $cache = new PhalApi_Cache_None();

        $rs = $this->phalApiCacheMulti->addCache($cache);
    }

    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'multiKey';
        $value = 'haha~';
        $expire = '100';

        $rs = $this->phalApiCacheMulti->set($key, $value, $expire);
    }

    /**
     * @group testGet
     * @depends testSet
     */ 
    public function testGet()
    {
        $key = 'multiKey';

        $rs = $this->phalApiCacheMulti->get($key);

        $this->assertSame('haha~', $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'multiKey';

        $rs = $this->phalApiCacheMulti->delete($key);
    }

}
