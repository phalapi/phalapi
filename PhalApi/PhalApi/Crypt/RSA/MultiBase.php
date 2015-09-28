<?php
/**
 * PhalApi_Crypt_RSA_MultiBase RSA加密层超类
 * 
 * 基于RSA非对称加密的层超类 - 超长字符串的应对方案
 *
 * - 考虑到RSA对加密长度的限制，这里采用了分段加密
 * - 结合josn和base64编码作为中间层转换，只能与对应的加解密结合使用
 * - 只适合字符串的加密，其他类型会强制转成字符串
 *
 * @package     PhalApi\Crypt\RSA
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-03-14
 */

abstract class PhalApi_Crypt_RSA_MultiBase implements PhalApi_Crypt {
	
	/**
	 * @var int 用户最大分割长度
	 */
	protected $maxSplitLen;
	
	/**
	 * @var int 允许最大分割的长度
	 */
    const ALLOW_MAX_SPLIT_LEN = 117;

	/**
	 * @param int $maxSplitLen 最大分割的彻底，应介于(0, PhalApi_Crypt_RSA_MultiBase::ALLOW_MAX_SPLIT_LEN]
	 */
    public function __construct($maxSplitLen = 0)
    {
        $this->maxSplitLen = $maxSplitLen > 0 
            ? min($maxSplitLen, self::ALLOW_MAX_SPLIT_LEN) : self::ALLOW_MAX_SPLIT_LEN;
    }

	/**
	 * @param string $data 待加密的字符串，注意其他类型会强制转成字符串再处理
     * @param string $key 私钥/公钥
     * @return string 失败时返回NULL
	 */
    public function encrypt($data, $key) {
        $base64Data = base64_encode(strval($data));

        $base64DataArr = str_split($base64Data, $this->getMaxSplitLen());

        $encryptPieCollector = array();
        foreach ($base64DataArr as $toCryptPie) {
            $encryptPie = $this->doEncrypt($toCryptPie, $key);
            if ($encryptPie === NULL) {
                return NULL;
            }
            $encryptPieCollector[] = base64_encode($encryptPie);
        }

        return base64_encode(json_encode($encryptPieCollector));
    }

    /**
     * 具体的加密操作
     * @param string $toCryptPie 待加密的片段
     * @param string $key 公钥/私钥
     */
    abstract protected function doEncrypt($toCryptPie, $key);

	/**
	 * @param string $data 待解密的字符串
	 * @param string $key 公钥/私钥
     * @return string 失败时返回NULL
	 */
    public function decrypt($data, $key){
        if ($data === NULL || $data === '') {
            return $data;
        }

        $encryptPieCollector = @json_decode(base64_decode($data), true);
        if (!is_array($encryptPieCollector)) {
            return NULL;
        }

        $decryptPieCollector = array();
        foreach ($encryptPieCollector as $encryptPie) {
            $base64DecryptPie = @base64_decode($encryptPie);
            if ($base64DecryptPie === FALSE) {
                return NULL;
            }
            $decryptPie = $this->doDecrypt($base64DecryptPie, $key);
            if ($decryptPie === NULL) {
                return NULL;
            }
            $decryptPieCollector[] = $decryptPie;
        }

        $decryptData = implode('', $decryptPieCollector);

        $rs = @base64_decode($decryptData);

        return $rs !== FALSE ? $rs : NULL;
    }

    /**
     * 具体加密的操作
     * @param string $encryptPie 待加密的片段
     * @param string $key 公钥/私钥
     */
    abstract protected function doDecrypt($encryptPie, $key);

    /**
     * 取用户设置的取大分割长度
     */
    protected function getMaxSplitLen()
    {
        return $this->maxSplitLen;
    }
}
