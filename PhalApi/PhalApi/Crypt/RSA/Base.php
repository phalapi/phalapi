<?php
/**
 * 基于RSA非对称加密的层超类
 *
 * - 考虑到RSA对加密长度的限制，这里采用了分段加密
 * - 另外出于方便落地存储，使用了base64编码
 * - 只适合字符串的加密，其他类型会强制转成字符串
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

abstract class PhalApi_Crypt_RSA_Base implements PhalApi_Crypt {

    const ALLOW_MAX_SPLIT_LEN = 117;

    protected $maxSplitLen;

	/**
	 * @param int $maxSplitLen 最大分割的彻底，应介于(0, self::ALLOW_MAX_SPLIT_LEN]
	 */
    public function __construct($maxSplitLen = 0)
    {
        $this->maxSplitLen = $maxSplitLen > 0 
            ? min($maxSplitLen, self::ALLOW_MAX_SPLIT_LEN) : self::ALLOW_MAX_SPLIT_LEN;
    }

	/**
	 * @param string $data 待加密的字符串，注意其他类型会强制转成字符串再处理
	 * @param string $key 私钥/公钥
	 */
    public function encrypt($data, $key) {
        $base64Data = base64_encode(strval($data));

        $base64DataArr = str_split($base64Data, $this->getMaxSplitLen());

        $encryptPieCollector = array();
        foreach ($base64DataArr as $toCryptPie) {
            $encryptPie = '';
            if ($this->doEncrypt($toCryptPie, $encryptPie, $key) === FALSE) {
                return NULL;
            }
            $encryptPieCollector[] = $encryptPie;
        }

        return base64_encode(serialize($encryptPieCollector));
    }

    abstract protected function doEncrypt($toCryptPie, &$encryptPie, $key);

	/**
	 * @param string $data 待解密的字符串
	 * @param string $key 公钥/私钥
	 */
    public function decrypt($data, $key){
        if ($data === NULL || $data === '') {
            return $data;
        }

        $encryptPieCollector = @unserialize(@base64_decode($data));
        if (!is_array($encryptPieCollector)) {
            return NULL;
        }

        $decryptPieCollector = array();
        foreach ($encryptPieCollector as $encryptPie) {
            $decryptPie = '';
            if ($this->doDecrypt($encryptPie, $decryptPie, $key) === FALSE) {
                return NULL;
            }
            $decryptPieCollector[] = $decryptPie;
        }

        $decryptData = implode('', $decryptPieCollector);

        $rs = @base64_decode($decryptData);

        return $rs !== FALSE ? $rs : NULL;
    }

    abstract protected function doDecrypt($encryptPie, &$decryptPie, $key);

    protected function getMaxSplitLen()
    {
        return $this->maxSplitLen;
    }
}
