<?php
/**
 * PhalApi_Cache_Redis Redis缓存
 *
 * - 使用序列化对需要存储的值进行转换，以提高速度
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license
 * @link        http://www.phalapi.net/
 * @author      zzguo   2015-5-11
 */

class PhalApi_Redis implements PhalApi_Cache {

    protected $redis = null;

    protected $auth = null;

    protected $prefix;

    /**
     * @param string $config['host'] Redis域名
     * @param int $config['port'] Redis端口,默认为6379
     * @param string $config['prefix'] Redis key prefix
     * @param string $config['auth'] Redis 身份验证
     */
    public function __construct($config) {
        $this->redis = new Redis();
        $this->redis->pconnect($config['host'], $config['port'],300);
	
	    $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'phalapi_';

        $this->auth = isset($config['auth']) ? $config['auth'] : '';

        if($this->auth != ''){
            $this->redis->auth($this->auth);
        }


    }
    /**
    * @function set
    * @param $key,$value 
    * @description : 将value 的值赋值给key
    */
    public function set($key,$value){
        $this->redis->set($key,$formatKey($value));
    }
    /**
    * @function setex
    * @param $key,$value,$expire
    * @description : 将value 的值赋值给key,生存时间为expire毫秒
    */
    public function setex($key, $value, $expire = 300) {
        $this->redis->setex($this->formatKey($key),$expire,@serialize($value));
    }
    /**
    * @function setnx
    * @param $key,$value 
    * @description : 检测是否存在key,若不存在则赋值value
    */
    public function setnx($key,$value){
        $thi->redis->setnx($key,$value);
    }

    public function get($key) {
		$value = $this->redis->get($this->formatKey($key));
        return $value !== FALSE ? @unserialize($value) : NULL;
    }

    public function delete($key) {
        return $this->redis->delete($this->formatKey($key));
    }

    protected function formatKey($key) {
		return $this->prefix . $key;
    }

    }
}
