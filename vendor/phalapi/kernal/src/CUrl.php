<?php
namespace PhalApi;

use PhalApi\Exception\InternalServerErrorException;

/**
 * CUrl CURL请求类
 *
 * 通过curl实现的快捷方便的接口请求类
 * 
 * <br>示例：<br>
 * 
```
 *  // 失败时再重试2次
 *  $curl = new CUrl(2);
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

class CUrl {

    /**
     * 最大重试次数
     */
    const MAX_RETRY_TIMES = 10;

	/**
	 * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
	 */
    protected $retryTimes;
    
    protected $header = array();
    
    protected $option = array();
    
    protected $hascookie = FALSE;
    
    protected $cookie = array();

    protected $isThrowExcption = true;

	/**
	 * @param int $retryTimes 超时重试次数，默认为1
	 */
    public function __construct($retryTimes = 1) {
        $this->retryTimes = $retryTimes < static::MAX_RETRY_TIMES 
            ? $retryTimes : static::MAX_RETRY_TIMES;
    }

    /** ------------------ 核心使用方法 ------------------ **/

	/**
	 * GET方式的请求
	 * @param string $url 请求的链接
	 * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string|boolean 接口返回的内容，超时返回false
	 */
    public function get($url, $timeoutMs = 3000) {
        return $this->request($url, array(), $timeoutMs, 'GET');
    } 

    /**
     * POST方式的请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string|boolean 接口返回的内容，超时返回false
     */
    public function post($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs, 'POST');
    }

    /**
     * PUT方式的请求
     * @param string $url 请求的链接
     * @param array $data PUT的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string|boolean 接口返回的内容，超时返回false
     */
    public function put($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs, 'PUT');
    }

    /**
     * DELETE方式的请求
     * @param string $url 请求的链接
     * @param array $data DELETE的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string|boolean 接口返回的内容，超时返回false
     */
    public function delete($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs, 'DELETE');
    }

    /**
     * PATCH方式的请求
     * @param string $url 请求的链接
     * @param array $data PATCH的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string|boolean 接口返回的内容，超时返回false
     */
    public function patch($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs, 'PATCH');
    }

    /** ------------------ 前置方法 ------------------ **/
    
    /**
     * 设置请求头，后设置的会覆盖之前的设置
     *
     * @param array $header 传入键值对如：
```     
     * array(
     *     'Accept' => 'text/html',
     *     'Connection' => 'keep-alive',
     * )
```     
     *
     * @return $this
     */
    public function setHeader($header) {
        $this->header = array_merge($this->header, $header);
        return $this;
    }
    
    /**
     * 设置curl配置项
     *
     * - 1、后设置的会覆盖之前的设置
     * - 2、开发者设置的会覆盖框架的设置
     *
     * @param array $option 格式同上
     *
     * @return $this
     */
    public function setOption($option) {
        $this->option = $option + $this->option;
        return $this;
    }

    /**
     * 设置是否当错误时抛出异常
     * @param boolean $isThrow true|false
     * @return $this
     */
    public function setIsThrowException($isThrow) {
        $this->isThrowExcption = $isThrow ? true : false;
        return $this;
    }
    
    /**
     * @param array $cookie
     */
    public function setCookie($cookie) {
        $this->cookie = $cookie;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getCookie() {
        return $this->cookie;
    }
    
    public function withCookies() {
        $this->hascookie = TRUE;

        if (!empty($this->cookie)) {
            $this->setHeader(array('Cookie' => $this->getCookieString()));
        }
        $this->setOption(array(CURLOPT_COOKIEFILE => ''));

        return $this;
    }

    /** ------------------ 辅助方法 ------------------ **/

    /**
     * 统一接口请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @param string $requestMethod 请求方式，例如：GET/POST/PUT/DELETE，不确定服务器支持这个自定义方法则不要使用它。
     * @return string 接口返回的内容，超时返回false，异常取消抛出时返回NULL
     * @throws Exception
     */
    public function request($url, $data, $timeoutMs = 3000, $requestMethod = NULL) {
        $rs = NULL;

        $options = array(
            CURLOPT_URL                 => $url,
            CURLOPT_RETURNTRANSFER      => TRUE,
            CURLOPT_HEADER              => 0,
            CURLOPT_TIMEOUT_MS          => $timeoutMs,
            CURLOPT_CONNECTTIMEOUT_MS   => $timeoutMs,
            CURLOPT_HTTPHEADER          => $this->getHeaders(),
        );

        // 请求方式
        $requestMethod = strtoupper($requestMethod);
        if ($requestMethod) {
            $options[CURLOPT_CUSTOMREQUEST] = $requestMethod;
        }
        if ($requestMethod == 'POST') {
            $options[CURLOPT_POST] = TRUE;
        }
        
        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        
        $options = $this->option + $options; // $this->>option优先
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while ($rs === FALSE && $curRetryTimes >= 0);
        $errno = curl_errno($ch);
        if ($errno && $this->isThrowExcption) {
            throw new InternalServerErrorException(sprintf("%s %s (Curl error: %d)\n", $url, curl_error($ch), $errno));
        }

        // update cookie
        if ($this->hascookie) {
            $cookie = $this->getRetCookie(curl_getinfo($ch, CURLINFO_COOKIELIST));
            !empty($cookie) && $this->cookie = $cookie + $this->cookie;
            $this->hascookie = FALSE;
            unset($this->header['Cookie']);
            unset($this->option[CURLOPT_COOKIEFILE]);
        }

        curl_close($ch);

        return $rs;
    }
    
    /**
     *
     * @return array
     */
    protected function getHeaders() {
        $arrHeaders = array();
        foreach ($this->header as $key => $val) {
            $arrHeaders[] = $key . ': ' . $val;
        }
        return $arrHeaders;
    }
    
    protected function getRetCookie(array $cookies) {
        $ret = array();
        foreach ($cookies as $cookie) {
            $arr = explode("\t", $cookie);
            if (!isset($arr[6])) {
                continue;
            }
            $ret[$arr[5]] = $arr[6];
        }
        return $ret;
    }
    
    protected function getCookieString() {
        $ret = '';
        foreach ($this->getCookie() as $key => $val) {
            $ret .= $key . '=' . $val . ';';
        }
        return trim($ret, ';');
    }
}
