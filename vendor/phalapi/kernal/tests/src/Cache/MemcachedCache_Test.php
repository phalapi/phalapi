<?php
namespace PhalApi\Tests;

use PhalApi\Cache\MemcachedCache;

include_once dirname(__FILE__) . '/memcached.php';

/**
 * PhpUnderControl_PhalApiCacheMemcached_Test
 *
 * 针对 ../../PhalApi/Cache/Memcached.php PhalApi_Cache_Memcached 类的PHPUnit单元测试
 *
 * @author: dogstar 20170406
 */

class PhpUnderControl_PhalApiCacheMemcached_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiCacheMemcached;

    protected function setUp()
    {
        parent::setUp();

        $config = array('host' => '127.0.0.1', 'port' => '11211');
        $this->phalApiCacheMemcached = new MemcachedCache($config);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'memcached-key';
        $value = 'ECO';
        $expire = 60;

        $rs = $this->phalApiCacheMemcached->set($key, $value, $expire);

        $this->assertEquals('ECO', $this->phalApiCacheMemcached->get($key));
    }

    public function testMultiMemcacheInstance()
    {
        $config = array(
            'host' => '127.0.0.1, 127.0.0.1', 
            'port' => '11211, 11211',
            'weight' => '20, 80',
        );

        $memcache = new MemcachedCache($config);

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

        $memcache = new MemcachedCache($config);

        $memcache->set('multi-key-4', 'M4', 60);
        $this->assertEquals('M4', $memcache->get('multi-key-4'));
    }

}
