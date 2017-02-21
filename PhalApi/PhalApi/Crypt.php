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
 * PhalApi_Crypt对称加密接口
 *
 * @package     PhalApi\Crypt
 * @license     http://www.phalapi.net/license GPL GPL License
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
