<?php
namespace PhalApi\Crypt\RSA;

use PhalApi\Crypt;

/**
 * Pri2PubCrypt RSA原始加密
 * 
 * RSA - 私钥加密，公钥解密
 *
 * @package     PhalApi\Crypt\RSA
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

class Pri2PubCrypt implements Crypt {

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
