<?php
namespace PhalApi\Cache;

use PhalApi\Cache;
use PhalApi\Exception\InternalServerErrorException;

/**
 * APCUCache    APC User Cache 
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-04-14
 */

class APCUCache {

    public function __construct() {
        if (!extension_loaded('apcu')) {
            throw new InternalServerErrorException(
                \PhalApi\T('missing {name} extension', array('name' => 'apcu'))
            );
        }
    }

    public function set($key, $value, $expire = 600) {
        return apcu_store($key, $value, $expire);
    }

    public function get($key) {
        $value = apcu_fetch($key);
        return $value !== FALSE ? $value : NULL;
    }

    public function delete($key) {
        return apcu_delete($key);
    }

    /**
     * 拉取缓存，拉取后同时删除缓存
     * @return minxed|NULL 缓存不存在时返回NULL
     */
    public function pull($key) {
        $value = $this->get($key);
        $this->delete($key);
        return $value;
    }
}
