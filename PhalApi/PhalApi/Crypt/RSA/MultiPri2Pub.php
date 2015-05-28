<?php
/**
 * PhalApi_Crypt_RSA_MultiPri2Pub 超长RSA加密
 * 
 * RSA - 私钥加密，公钥解密 - 超长字符串的应对方案
 *
 * @package     PhalApi\Crypt\RSA
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

class PhalApi_Crypt_RSA_MultiPri2Pub extends PhalApi_Crypt_RSA_MultiBase {

    protected $pri2pub;

    public function __construct() {
        $this->pri2pub = new PhalApi_Crypt_RSA_Pri2Pub();

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $prikey) {
        return $this->pri2pub->encrypt($toCryptPie, $prikey);
    }

    protected function doDecrypt($encryptPie, $prikey) {
        return $this->pri2pub->decrypt($encryptPie, $prikey);
    }
}
