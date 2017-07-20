<?php
namespace PhalApi\Cache;

use PhalApi\Cache;
use PhalApi\Exception\InternalServerErrorException;

/**
 * RedisCache Redis缓存
 *
 * - 使用序列化对需要存储的值进行转换，以提高速度
 * - 提供更多redis的操作，以供扩展类库使用
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      zzguo   2015-5-11
 * @modify      dogstar <chanzonghuang@gmail.com> 20150516
 */

class RedisCache implements Cache {

    protected $redis;

    protected $auth;

    protected $prefix;

    /**
     * @param string $config['type']    Redis连接方式 unix,http
     * @param string $config['socket']  unix方式连接时，需要配置
     * @param string $config['host']    Redis域名
     * @param int    $config['port']    Redis端口,默认为6379
     * @param string $config['prefix']  Redis key prefix
     * @param string $config['auth']    Redis 身份验证
     * @param int    $config['db']      Redis库,默认0
     * @param int    $config['timeout'] 连接超时时间,单位秒,默认300
     */
    public function __construct($config) {
        $this->redis = new \Redis();

        // 连接
        if (isset($config['type']) && $config['type'] == 'unix') {
            if (!isset($config['socket'])) {
                throw new InternalServerErrorException(\PhalApi\T('redis config key [socket] not found'));
            }
            $this->redis->connect($config['socket']);
        } else {
            $port = isset($config['port']) ? intval($config['port']) : 6379;
            $timeout = isset($config['timeout']) ? intval($config['timeout']) : 300;
            $this->redis->connect($config['host'], $port, $timeout);
        }

        // 验证
        $this->auth = isset($config['auth']) ? $config['auth'] : '';
        if ($this->auth != '') {
            $this->redis->auth($this->auth);
        }

        // 选择
        $dbIndex = isset($config['db']) ? intval($config['db']) : 0;
        $this->redis->select($dbIndex);

        $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'phalapi:';
    }

    /**
     * 将value 的值赋值给key,生存时间为expire秒
     */
    public function set($key, $value, $expire = 600) {
        $this->redis->setex($this->formatKey($key), $expire, $this->formatValue($value));
    }

    public function get($key) {
        $value = $this->redis->get($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function delete($key) {
        return $this->redis->delete($this->formatKey($key));
    }

    /**
     * 检测是否存在key,若不存在则赋值value
     */
    public function setnx($key, $value) {
        return $this->redis->setnx($this->formatKey($key), $this->formatValue($value));
    }

    public function lPush($key, $value) {
        return $this->redis->lPush($this->formatKey($key), $this->formatValue($value));
    }

    public function rPush($key, $value) {
        return $this->redis->rPush($this->formatKey($key), $this->formatValue($value));
    }

    public function lPop($key) {
        $value = $this->redis->lPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function rPop($key) {
        $value = $this->redis->rPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    protected function formatKey($key) {
        return $this->prefix . $key;
    }

    protected function formatValue($value) {
        return @serialize($value);
    }

    protected function unformatValue($value) {
        return @unserialize($value);
    }
}
