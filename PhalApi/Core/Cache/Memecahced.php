<?php
/**
 * MC缓存
 *
 * - 使用序列化对需要存储的值进行转换，以提高速度
 *
 * @author: dogstar 2014-11-14
 */

class Core_Cache_Memecahced implements Core_Cache
{
    private $memcached = null;

    public function __construct($config)
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer($config['host'], $config['port']);
    }

    public function set($key, $value, $expire = 600)
    {
        $this->memcached->set($key, serialize($value), $expire);
    }

    public function get($key)
    {
        return unserialize($this->memcached->get($key));
    }

    public function delete($key)
    {
        return $this->memcached->delete($key);
    }
}
