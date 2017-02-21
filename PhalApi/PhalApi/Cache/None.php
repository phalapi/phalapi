<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * PhalApi_Cache_None 空缓存 - NULL-Object空对象模式
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

class PhalApi_Cache_None implements PhalApi_Cache {

	public function set($key, $value, $expire = 600) {
	}

    public function get($key) {
		return NULL;
	}

    public function delete($key) {
	}
}
