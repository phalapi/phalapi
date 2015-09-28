<?php
/**
 * PhalApi_CUrl CURL请求类
 *
 * 通过curl实现的快捷方便的接口请求类
 * 
 * <br>示例：<br>
 * 
```
 *  // 失败时再重试2次
 *  $curl = new PhalApi_CUrl(2);
 *
 *  // GET
 *  $rs = $curl->get('http://phalapi.oschina.mopaas.com/Public/demo/?service=Default.Index');
 *
 *  // POST
 *  $data = array('username' => 'dogstar');
 *  $rs = $curl->post('http://phalapi.oschina.mopaas.com/Public/demo/?service=Default.Index', $data);
```
 *
 * @package     PhalApi\CUrl
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-01-02
 */

class PhalApi_CUrl {

    /**
     * 最大重试次数
     */
    const MAX_RETRY_TIMES = 10;

	/**
	 * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
	 */
    protected $retryTimes;

	/**
	 * @param int $retryTimes 超时重试次数，默认为1
	 */
    public function __construct($retryTimes = 1) {
        $this->retryTimes = $retryTimes < self::MAX_RETRY_TIMES 
            ? $retryTimes : self::MAX_RETRY_TIMES;
    }

	/**
	 * GET方式的请求
	 * @param string $url 请求的链接
	 * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string 接口返回的内容，超时返回false
	 */
    public function get($url, $timeoutMs = 3000) {
        return $this->request($url, FALSE, $timeoutMs);
    } 

    /**
     * POST方式的请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    public function post($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs);
    }

    /**
     * 统一接口请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string 接口返回的内容，超时返回false
     */
    protected function request($url, $data, $timeoutMs = 3000) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while($rs === FALSE && $curRetryTimes >= 0);

        curl_close($ch);

        return $rs;
    }
}
