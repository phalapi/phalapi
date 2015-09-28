<?php
/**
 * PhalApi_Crypt_MultiMcrypt 多级mcrypt加密
 * 对底层的mcrypt进行简单的再封装，以便存储和保留类型
 *
 * - 依赖PhalApi_Crypt_Mcrypt进行加解密操作
 * - 支持任何数据类型的加解密
 * - 返回便于存储的字符串
 *
 * @package     PhalApi\Crypt
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-11
 */

class PhalApi_Crypt_MultiMcrypt implements PhalApi_Crypt {

	/**
	 * @var PhalApi_Crypt_Mcrypt $mcrypt
	 */
    protected $mcrypt = NULL;

    public function __construct($iv) {
        $this->mcrypt = new PhalApi_Crypt_Mcrypt($iv);
    }

    /**
     * @param mixed $data 待加密的数据
     */
    public function encrypt($data, $key) {
        $encryptData = serialize($data);

        $encryptData = $this->mcrypt->encrypt($encryptData, $key);

        $encryptData = base64_encode($encryptData);

        return $encryptData;
    }

    /**
     * 忽略不能正常反序列化的操作，并且在不能预期解密的情况下返回原文
     */
    public function decrypt($data, $key) {
        $decryptData = base64_decode($data);

        if ($decryptData === FALSE || $decryptData === '') {
            return $data;
        }

        $decryptData = $this->mcrypt->decrypt($decryptData, $key);

        $decryptData = @unserialize($decryptData);
        if ($decryptData === FALSE) {
            return $data;
        }

        return $decryptData;
    }
}
