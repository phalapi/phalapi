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
 * Config Interface
 *
 * Get all configurations of project.
 * 
 * <br>Usage:<br>
```
 * // Assume we have the app.php config file as below:
 * return array(
 *  'version' => '1.1.1',
 * 
 *  'email' => array(
 *      'address' => 'chanzonghuang@gmail.com',
 *   );
 * );
 *
 * // We can get the config like:
 * // get all configs in app.php
 * DI()->config->get('app');
 * 
 * // or one config in app.php
 * DI()->config->get('app.version');  // return: 1.1.1
 * 
 * // or multi config in app.php
 * DI()->config->get('app.version.address');  // return: chanzonghuang@gmail.com
```
 *
 * @package PhalApi\Config
 * @license http://www.phalapi.net/license GPL GPL License
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

interface PhalApi_Config {

	/**
     * Get config
     * 
     * @param 	$key 	string 		config key
     * @param 	mixed 	$default 	config default value
     * @return 	mixed 	config value, or return $default when config not exists
     */
	public function get($key, $default = NULL);
}
