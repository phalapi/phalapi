<?php
/**
 * 使用mcrypt扩展进加解密
 *
 * @link: http://php.net/manual/zh/function.mcrypt-generic.php
 * @author: dogstar 2014-12-10
 */

class PhalApi_Crypt_Mcrypt implements PhalApi_Crypt
{
    protected $iv;

    const MAX_IV_SIZE = 8;
    const MAX_KEY_LENGTH = 56;

    public function __construct($iv = '********')
    {
        $this->iv = str_pad($iv, self::MAX_IV_SIZE, '*');
        if (strlen($this->iv) > self::MAX_IV_SIZE) {
            $this->iv = substr($this->iv, 0, self::MAX_IV_SIZE);
        }
    }

    public function encrypt($data, $key)
    {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $encrypted = mcrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return $encrypted;
    }

    public function decrypt($data, $key)
    {
        if ($data === '') {
            return $data;
        }

        $cipher = $this->createCipher($key);

        $decrypted = mdecrypt_generic($cipher, $data);

        $this->clearCipher($cipher);

        return rtrim($decrypted, "\0");
    }

    protected function createCipher($key)
    {
        $cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');

        if ($cipher === FALSE || $cipher < 0) {
            throw new PhalApi_Exception_InternalServerError(T('mcrypt_module_open with {cipher}', array('cipher' => $cipher)));
        }

        mcrypt_generic_init($cipher, $this->formatKey($key), $this->iv);

        return $cipher;
    }

    protected function formatKey($key)
    {
        return strlen($key) > self::MAX_KEY_LENGTH ?  substr($key, 0, self::MAX_KEY_LENGTH) : $key;
    }

    protected function clearCipher($cipher)
    {
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);
    }
}

