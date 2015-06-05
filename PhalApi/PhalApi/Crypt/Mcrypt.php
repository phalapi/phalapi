<?php
/**
 * PhalApi_Crypt_Mcrypt 原始mcrypt加密
 * 
 * 使用mcrypt扩展进加解密
 *
 * <br>使用示例：<br>
```
 *  $mcrypt = new PhalApi_Crypt_Mcrypt('12345678');
 *
 *  $data = 'dogstar love php';
 *  $key = 'secrect';
 *
 *  // 加密
 *  $encryptData = $mcrypt->encrypt($data, $key);
 *
 *  // 解密
 *  $decryptData = $mcrypt->decrypt($encryptData, $key);
```
 *
 * @package     PhalApi\Crypt
 * @link        http://php.net/manual/zh/function.mcrypt-generic.php
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

class PhalApi_Crypt_Mcrypt implements PhalApi_Crypt {

	/**
	 * @var string $iv 加密向量， 最大长度不得超过PhalApi_Crypt_Mcrypt::MAX_IV_SIZE
	 */
    protected $iv;

    /**
     * @var int 最大加密向量长度
     */
    const MAX_IV_SIZE = 8;
    
    /**
     * @var int 最大加密key的长度
     */
    const MAX_KEY_LENGTH = 56;

    /**
     * @param string $iv 加密的向量 最大长度不得超过 MAX_IV_SIZE
     */
    public function __construct($iv = '********') {
        $this->iv = str_pad($iv, self::MAX_IV_SIZE, '*');
        if (strlen($this->iv) > self::MAX_IV_SIZE) {
            $this->iv = substr($this->iv, 0, self::MAX_IV_SIZE);
        }
    }

    /**
     * 对称加密 
     *
     * @param string $data 待加密的数据
     * @param string key 私钥
     * @return string 加密后的数据
     */
    public function encrypt($data, $key) {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $encrypted = mcrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return $encrypted;
    }

    /**
     * 对称解密
     *
     * @see PhalApi_Crypt_Mcrypt::encrypt()
     * 
     * @param string $data 待解密的数据
     * @param string key 私钥
     * @return string 解密后的数据
     */
    public function decrypt($data, $key) {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $decrypted = mdecrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return rtrim($decrypted, "\0");
    }

    /**
     * 创建cipher
     * @param string $key 私钥
     * @return resource
     * @throws PhalApi_Exception_InternalServerError
     */
    protected function createCipher($key) {
        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');

        if ($cipher === FALSE || $cipher < 0) {
            throw new PhalApi_Exception_InternalServerError(
                T('mcrypt_module_open with {cipher}', array('cipher' => $cipher))
            );
        }

        mcrypt_generic_init($cipher, $this->formatKey($key), $this->iv);

        return $cipher;
    }

    /**
     * 格式化私钥
     * @param string $key 私钥
     */
    protected function formatKey($key) {
        return strlen($key) > self::MAX_KEY_LENGTH ?  substr($key, 0, self::MAX_KEY_LENGTH) : $key;
    }

    /**
     * 释放cipher
     * @param resource $cipher
     */
    protected function clearCipher($cipher) {
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);
    }
}

