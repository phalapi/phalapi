<?php
/**
 * 对底层的mcrypt进行简单的再封装，以便存储和保留类型
 *
 * @author: dogstar 2014-12-11
 */

class PhalApi_Crypt_MultiMcrypt implements PhalApi_Crypt
{
    protected $mcrypt = null;

    public function __construct($iv)
    {
        $this->mcrypt = new PhalApi_Crypt_Mcrypt($iv);
    }

    public function encrypt($data, $key)
    {
        $encryptData = serialize($data);

        $encryptData = $this->mcrypt->encrypt($encryptData, $key);

        $encryptData = base64_encode($encryptData);

        return $encryptData;
    }

    /**
     * 忽略不能正常反序列化的操作，并且在不能预期解密的情况下返回原文
     */
    public function decrypt($data, $key)
    {
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
