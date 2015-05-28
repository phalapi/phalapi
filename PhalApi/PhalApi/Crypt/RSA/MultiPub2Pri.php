<?php
/**
 * PhalApi_Crypt_RSA_MultiPub2Pri 超长RSA加密
 * 
 * RSA - 公钥加密，私钥解密 - 超长字符串的应对方案
 *
 * @package     PhalApi\Crypt\RSA
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-03-15
 */

class PhalApi_Crypt_RSA_MultiPub2Pri extends PhalApi_Crypt_RSA_MultiBase {

    protected $pub2pri;

    public function __construct() {
        $this->pub2pri = new PhalApi_Crypt_RSA_Pub2Pri();

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $pubkey) {
        return $this->pub2pri->encrypt($toCryptPie, $pubkey);
    }

    protected function doDecrypt($encryptPie, $prikey) {
        return $this->pub2pri->decrypt($encryptPie, $prikey);
    }
}
