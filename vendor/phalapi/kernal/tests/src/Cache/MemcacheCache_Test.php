<?php
namespace PhalApi\Tests;

use PhalApi\Cache\MemcacheCache;

/**
 * PhpUnderControl_PhalApiCacheMemcache_Test
 *
 * 针对 ../../PhalApi/Cache/Memcache.php PhalApi_Cache_Memcache 类的PHPUnit单元测试
 *
 * @author: dogstar 20150507
 */

class PhpUnderControl_PhalApiCacheMemcache_Test extends \PHPUnit_Framework_TestCase
{
    public $memcacheCache;

    protected function setUp()
    {
        parent::setUp();

        $config = array('host' => '127.0.0.1', 'port' => '11211');
        $this->memcacheCache = new MemcacheCache($config);
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

        $this->memcacheCache->set($key, $value, $expire);

        $this->assertEquals('phalapi', $this->memcacheCache->get($key));
    }

    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'no-this-key';

        $rs = $this->memcacheCache->get($key);

        $this->assertSame(NULL, $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'key-2015-05-07';

        $this->assertNotNull($this->memcacheCache->get($key));

        $this->memcacheCache->delete($key);

        $this->assertNull($this->memcacheCache->get($key));
    }

    public function testMultiMemcacheInstance()
    {
        $config = array(
            'host' => '127.0.0.1, 127.0.0.1', 
            'port' => '11211, 11211',
            'weight' => '20, 80',
        );

        $memcache = new MemcacheCache($config);

        $memcache->set('multi-key-1', 'M1', 60);
        $memcache->set('multi-key-2', 'M2', 60);
        $memcache->set('multi-key-3', 'M3', 60);

        $this->assertEquals('M1', $memcache->get('multi-key-1'));
        $this->assertEquals('M2', $memcache->get('multi-key-2'));
        $this->assertEquals('M3', $memcache->get('multi-key-3'));
    }

    public function testMultiMemcacheInstanceUnformer()
    {
        $config = array(
            'host' => '127.0.0.1, 127.0.0.1', 
            'port' => '11211, 11211',   // same as 11211, 11212
            'weight' => '20',           // same as 20, 0
        );

        $memcache = new MemcacheCache($config);

        $memcache->set('multi-key-4', 'M4', 60);
        $this->assertEquals('M4', $memcache->get('multi-key-4'));
    }

}
