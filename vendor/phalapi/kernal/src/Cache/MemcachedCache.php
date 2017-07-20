<?php
namespace PhalApi\Cache;

use PhalApi\Cache\MemcacheCache;

/**
 * MemcachedCache MC缓存
 *
 * - 使用序列化对需要存储的值进行转换，以提高速度
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-11-14
 */

class MemcachedCache extends MemcacheCache {

    /**
     * 注意参数的微妙区别
     */
    public function set($key, $value, $expire = 600) {
        $this->memcache->set($this->formatKey($key), @serialize($value), $expire);
    }

    /**
     * 返回更高版本的MC实例
	 * @return Memcached
     */
    protected function createMemcache() {
        return new \Memcached();
    }
}
