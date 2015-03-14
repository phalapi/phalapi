<?php
/**
 * RSA - 私钥加密，公钥解密
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

class PhalApi_Crypt_RSA_Pri2Pub extends PhalApi_Crypt_RSA_Base {

    protected function doEncrypt($toCryptPie, &$encryptPie, $key) {
        return @openssl_private_encrypt($toCryptPie, $encryptPie, $key);
    }

    protected function doDecrypt($encryptPie, &$decryptPie, $key) {
        return @openssl_public_decrypt($encryptPie, $decryptPie, $key);
    }
}
