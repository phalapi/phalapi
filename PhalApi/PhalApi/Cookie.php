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
 * Cookie Operation
 * 
 * - simple wrapper fo PHP original COOKIE
 * - NOTE: the new cookie will works in next request time
 *
 * <br>Usage:<br>
```
 *  // COOKIE
 *  DI()->cookie = 'PhalApi_Cookie';
 *  
 *  // set COOKIE
 *  DI()->cookie->set('name', 'phalapi', $_SERVER['REQUEST_TIME'] + 600);
 *  
 *  // get COOKIE
 *  echo DI()->cookie->get('name');  // output phalapi
 *  
 *  // delete COOKIE
 *  DI()->cookie->delete('name');
 *  
```
 * @package PhalApi\Cookie
 * @license http://www.phalapi.net/license GPL GPL License
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2015-04-11
 */

class PhalApi_Cookie {

	/**
	 * COOKIE configuration
	 */
    protected $config = array();

	/**
	 * @param 	string 		$config['path'] 		cookie path
	 * @param 	string 		$config['domain'] 		cookie domain
	 * @param 	boolean 	$config['secure'] 		whether secure
	 * @param 	boolean 	$config['httponly'] 	whether http only
	 * @link 	http://php.net/manual/zh/function.setcookie.php
	 */
    public function __construct($config = array()) {
        $this->config['path']       = isset($config['path']) ? $config['path'] : NULL;
        $this->config['domain']     = isset($config['domain']) ? $config['domain'] : NULL;
        $this->config['secure']     = isset($config['secure']) ? $config['secure'] : FALSE;
        $this->config['httponly']   = isset($config['httponly']) ? $config['httponly'] : FALSE;
    }

	/**
	 * Get COOKIE
	 *
	 * @param 	string 				$name 	COOKIE name
	 * @return 	string/NULL/array 	return the whole $_COOKIE when $name is NUL; return cookie value when exists, or NULL when not exists
	 */
    public function get($name = NULL) {
        if ($name === NULL) {
            return $_COOKIE;
        }

        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
    }

	/**
	 * Set COOKIE
	 *
	 * @param 	string 			$name 		COOKIE name
	 * @param 	string/int 		$value 		COOKIE value, suggest to be some simple strings or number, not sensitive data
	 * @param 	int 			$expire 	expire timestamp, expire in one MONTH when $expire is NULL
	 * @param 	boolean
	 */
    public function set($name, $value, $expire = NULL) {
        if ($expire === NULL) {
            $expire = $_SERVER['REQUEST_TIME'] + 2592000;   //a month
        }

        return setcookie(
            $name, 
            $value, 
            $expire, 
            $this->config['path'], 
            $this->config['domain'], 
            $this->config['secure'], 
            $this->config['httponly']
        );
    }

	/**
	 * Delete COOKIE
	 *
	 * @param 	strint 		$name 		COOKIE name
	 * @param 	boolean
	 * @see 	PhalApi_Cookie::set()
	 */
    public function delete($name) {
        return $this->set($name, '', 0);
    }

	/**
	 * Get COOKIE config
	 */
    public function getConfig() {
        return $this->config;
    }
}
