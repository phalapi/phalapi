<?php
/**
 * RSA - 公钥加密，私钥解密
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-03-15
 */

class PhalApi_Crypt_RSA_Pub2Pri extends PhalApi_Crypt_RSA_Base {

    protected function doEncrypt($toCryptPie, &$encryptPie, $key) {
        return @openssl_public_encrypt($toCryptPie, $encryptPie, $key);
    }

    protected function doDecrypt($encryptPie, &$decryptPie, $key) {
        return @openssl_private_decrypt($encryptPie, $decryptPie, $key);
    }
}
