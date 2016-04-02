#!/usr/bin/env php
<?php
/**
 * PhpUnderControl_PhalApiCacheMemcache_Test
 *
 * 针对 ../../PhalApi/Cache/Memcache.php PhalApi_Cache_Memcache 类的PHPUnit单元测试
 *
 * @author: dogstar 20150507
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Cache_Memcache')) {
    require dirname(__FILE__) . '/../../PhalApi/Cache/Memcache.php';
}

class PhpUnderControl_PhalApiCacheMemcache_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCacheMemcache;

    protected function setUp()
    {
        parent::setUp();

        $config = array('host' => '127.0.0.1', 'port' => '11211');
        $this->phalApiCacheMemcache = new PhalApi_Cache_Memcache($config);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'key-2015-05-07';
        $value = 'phalapi';
        $expire = 60;

        $this->phalApiCacheMemcache->set($key, $value, $expire);

        $this->assertEquals('phalapi', $this->phalApiCacheMemcache->get($key));
    }

    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'no-this-key';

        $rs = $this->phalApiCacheMemcache->get($key);

        $this->assertSame(NULL, $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'key-2015-05-07';

        $this->assertNotNull($this->phalApiCacheMemcache->get($key));

        $this->phalApiCacheMemcache->delete($key);

        $this->assertNull($this->phalApiCacheMemcache->get($key));
    }

}
