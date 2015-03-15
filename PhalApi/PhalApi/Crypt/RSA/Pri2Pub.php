<?php
/**
 * RSA - 私钥加密，公钥解密
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

class PhalApi_Crypt_RSA_Pri2Pub implements PhalApi_Crypt {

    public function encrypt($data, $prikey) {
        $rs = '';

        if (@openssl_private_encrypt($data, $rs, $prikey) === FALSE) {
            return NULL;
        }

        return $rs;
    }

    public function decrypt($data, $pubkey) {
        $rs = '';

        if (@openssl_public_decrypt($data, $rs, $pubkey) === FALSE) {
            return NULL;
        }

        return $rs;
    }
}
