<?php
/**
 * RSA - 公钥加密，私钥解密
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-03-15
 */

class PhalApi_Crypt_RSA_Pub2Pri implements PhalApi_Crypt {

    public function encrypt($data, $pubkey) {
        $rs = '';

        if (@openssl_public_encrypt($data, $rs, $pubkey) === FALSE) {
            return NULL;
        }

        return $rs;
    }
    
    public function decrypt($data, $prikey) {
        $rs = '';

        if (@openssl_private_decrypt($data, $rs, $prikey) === FALSE) {
            return NULL;
        }

        return $rs;
    }
}
