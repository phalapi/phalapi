<?php
/**
 * 使用mcrypt扩展进加解密
 *
 * 示例：
 *
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
 *
 *
 * @link: http://php.net/manual/zh/function.mcrypt-generic.php
 * @author dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

class PhalApi_Crypt_Mcrypt implements PhalApi_Crypt {

    protected $iv;

    const MAX_IV_SIZE = 8;
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
     * @key string 私钥
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
     * @param string $data 待解密的数据
     * @key string 私钥
     * @return string 解密后的数据
     *
     * @see: PhalApi_Crypt_Mcrypt::encrypt()
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

    protected function formatKey($key) {
        return strlen($key) > self::MAX_KEY_LENGTH ?  substr($key, 0, self::MAX_KEY_LENGTH) : $key;
    }

    protected function clearCipher($cipher) {
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);
    }
}

