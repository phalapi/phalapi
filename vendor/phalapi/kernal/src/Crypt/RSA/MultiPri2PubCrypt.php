<?php
namespace PhalApi\Crypt\RSA;

use PhalApi\Crypt\RSA\MultiBase;
use PhalApi\Crypt\RSA\Pri2PubCrypt;

/**
 * MultiPri2PubCrypt 超长RSA加密
 * 
 * RSA - 私钥加密，公钥解密 - 超长字符串的应对方案
 *
 * @package     PhalApi\Crypt\RSA
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

class MultiPri2PubCrypt extends MultiBase {

    protected $pri2pub;

    public function __construct() {
        $this->pri2pub = new Pri2PubCrypt();

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $prikey) {
        return $this->pri2pub->encrypt($toCryptPie, $prikey);
    }

    protected function doDecrypt($encryptPie, $prikey) {
        return $this->pri2pub->decrypt($encryptPie, $prikey);
    }
}
