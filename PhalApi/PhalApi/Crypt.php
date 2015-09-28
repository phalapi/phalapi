<?php
/**
 * PhalApi_Crypt对称加密接口
 *
 * @package     PhalApi\Crypt
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

interface PhalApi_Crypt {

	/**
	 * 对称加密
	 * 
	 * @param mixed $data 等加密的数据
	 * @param string $key 加密的key
	 * @return mixed 加密后的数据
	 */
    public function encrypt($data, $key);
    
    /**
     * 对称解密
     * 
     * @see PhalApi_Crypt::encrypt()
     * @param mixed $data 对称加密后的内容
     * @param string $key 加密的key
     * @return mixed 解密后的数据
     */
    public function decrypt($data, $key);
}
