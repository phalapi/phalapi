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
 * Cache Interface
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

interface PhalApi_Cache {

	/**
	 * Set cache
	 * 
	 * @param 	string 	$key 		cache key
	 * @param 	mixed 	$value 		cache content data
	 * @param 	int 	$expire 	cache expire time, unit: seconds, not timestamp
	 */
    public function set($key, $value, $expire = 600);

    /**
     * Get cache
     * 
     * @param 	string 	$key cache key
     * @return 	mixed 	return null when fail
     */
    public function get($key);

    /**
     * Delete cache
     * 
     * @param string $key
     */
    public function delete($key);
}
