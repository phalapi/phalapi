<?php
/**
 * PhalApi_Cache_Redis Redis缓存
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

class PhalApi_Cache_Redis implements PhalApi_Cache {

	protected $redis;

	protected $auth;

	protected $prefix;

	/**
	 * @param string $config['host'] Redis域名
	 * @param int $config['port'] Redis端口,默认为6379
	 * @param string $config['prefix'] Redis key prefix
	 * @param string $config['auth'] Redis 身份验证
	 */
	public function __construct($config) {
        $this->redis = new Redis();

		$this->redis->pconnect($config['host'], $config['port'], 300);
		$this->prefix = isset($config['prefix']) ? $config['prefix'] : 'phalapi_';
        $this->auth = isset($config['auth']) ? $config['auth'] : '';

		if($this->auth != ''){
			$this->redis->auth($this->auth);
		}
	}
	/**
     * 将value 的值赋值给key,生存时间为expire秒
	 */
	public function set($key, $value, $expire = 600){
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
	public function setnx($key, $value){
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
