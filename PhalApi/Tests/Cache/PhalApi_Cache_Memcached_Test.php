<?php
/**
 * PhpUnderControl_PhalApiCacheMemcached_Test
 *
 * 针对 ../../PhalApi/Cache/Memcached.php PhalApi_Cache_Memcached 类的PHPUnit单元测试
 *
 * @author: dogstar 20170406
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Cache_Memcached')) {
    require dirname(__FILE__) . '/../../../PhalApi/Cache/Memcached.php';
}

class PhpUnderControl_PhalApiCacheMemcached_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCacheMemcached;

    protected function setUp()
    {
        parent::setUp();

        $config = array('host' => '127.0.0.1', 'port' => '11211');
        $this->phalApiCacheMemcached = new PhalApi_Cache_Memcached($config);
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
            'port' => '11211, 11212',
            'weight' => '20, 80',
        );

        $memcache = new PhalApi_Cache_Memcached($config);

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
            'port' => '11211, 11212',   // same as 11211, 11212
            'weight' => '20',           // same as 20, 0
        );

        $memcache = new PhalApi_Cache_Memcached($config);

        $memcache->set('multi-key-4', 'M4', 60);
        $this->assertEquals('M4', $memcache->get('multi-key-4'));
    }

}
