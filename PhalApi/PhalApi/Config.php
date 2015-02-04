<?php 
/**
 * PhalApi_Config 配置接口
 *
 * 获取系统所需要的参数配置
 *
 * @author dogstar 2014-10-02
 */

interface PhalApi_Config {

	/**
     * 获取配置
     * @param $key string 配置键值
     * @return mixed 需要获取的配置值
     */
	public function get($key, $default = null);
}
