<?php
/**
 * PhalApi_Crypt对称加密接口
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

interface PhalApi_Crypt {

	/**
	 * 对称加密
	 * 
	 * @param mixed $data 等加密的数据
	 * @param string $key 加密的key
	 */
    public function encrypt($data, $key);
    
    /**
     * 对称解密
     * @param mixed $data 对称加密后的内容{@see PhalApi_Crypt::encrypt()}
     * @param string $key 加密的key
     */
    public function decrypt($data, $key);
}
