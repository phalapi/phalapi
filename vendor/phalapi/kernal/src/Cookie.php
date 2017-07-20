<?php
namespace PhalApi;

/**
 * Cookie COOKIE操作
 * 
 * - 原生态COOKIE操作的简单封装
 * - 注意，设置的COOKIE需要在下一次才能生效
 *
 * <br>使用示例：<br>
```
 *  //COOKIE
 *  DI()->cookie = 'Cookie';
 *  
 *  //设置COOKIE服务
 *  DI()->cookie->set('name', 'phalapi', $_SERVER['REQUEST_TIME'] + 600);
 *  
 *  //获取
 *  echo DI()->cookie->get('name');  //输出 phalapi
 *  
 *  //删除
 *  DI()->cookie->delete('name');
 *  
```
 * @package PhalApi\Cookie
 * @license http://www.phalapi.net/license GPL 协议
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2015-04-11
 */

class Cookie {

	/**
	 * COOKIE配置
	 */
    protected $config = array();

	/**
	 * @param string $config['path'] 路径
	 * @param string $config['domain'] 域名
	 * @param boolean $config['secure'] 是否加密
	 * @param boolean $config['httponly'] 是否只HTTP协议
	 * @link http://php.net/manual/zh/function.setcookie.php
	 */
    public function __construct($config = array()) {
        $this->config['path']       = isset($config['path']) ? $config['path'] : NULL;
        $this->config['domain']     = isset($config['domain']) ? $config['domain'] : NULL;
        $this->config['secure']     = isset($config['secure']) ? $config['secure'] : FALSE;
        $this->config['httponly']   = isset($config['httponly']) ? $config['httponly'] : FALSE;
    }

	/**
	 * 获取COOKIE
	 *
	 * @param string $name 待获取的COOKIE名字
	 * @return string/NULL/array $name为NULL时返回整个$_COOKIE，存在时返回COOKIE，否则返回NULL
	 */
    public function get($name = NULL) {
        if ($name === NULL) {
            return $_COOKIE;
        }

        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
    }

	/**
	 * 设置COOKIE
	 *
	 * @param string $name 待设置的COOKIE名字
	 * @param string/int $value 建议COOKIE值为一些简单的字符串或数字，不推荐存放敏感数据
	 * @param int $expire 有效期的timestamp，为NULL时默认存放一个月
	 * @param boolean
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
	 * 删除COOKIE
	 *
	 * @param strint $name 待删除的COOKIE名字
	 * @param boolean
	 * @see Cookie::set()
	 */
    public function delete($name) {
        return $this->set($name, '', 0);
    }

	/**
	 * 获取COOKIE的配置
	 */
    public function getConfig() {
        return $this->config;
    }
}
