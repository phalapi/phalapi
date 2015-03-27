<?php 
/**
 * PhalApi_Config 配置接口
 *
 * 获取系统所需要的参数配置
 *
 * @package PhalApi\Config
 * @license http://www.phalapi.net/license
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

interface PhalApi_Config {

	/**
     * 获取配置
     * 
     * @param $key string 配置键值
     * @param mixed $default 缺省值
     * @return mixed 需要获取的配置值，不存在时统一返回$default
     */
	public function get($key, $default = NULL);
}
