<?php
/**
 * PhalApi_Response 响应类
 *
 * - 拥有各种结果返回状态 ，以及对返回结果 的格式化
 * - 其中：200成功，400非法请求，500服务器错误
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

abstract class PhalApi_Response {

	/**
	 * @var int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
	 */
    protected $ret = 200;
    
    /**
     * @var array 待返回给客户端的数据
     */
    protected $data = array();
    
    /**
     * @var string $msg 错误返回信息
     */
    protected $msg = '';
    
    /**
     * @var array $headers 响应报文头部
     */
    protected $headers = array();

    /** ------------------ setter ------------------ **/

    /**
     * 设置返回状态码
     * @param int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     * @return PhalApi_Response
     */
    public function setRet($ret) {
    	$this->ret = $ret;
    	return $this;
    }
    
    /**
     * 设置返回数据
     * @param array/string $data 待返回给客户端的数据，建议使用数组，方便扩展升级
     * @return PhalApi_Response
     */
    public function setData($data) {
    	$this->data = $data;
    	return $this;
    }
    
    /**
     * 设置错误信息
     * @param string $msg 错误信息
     * @return PhalApi_Response
     */
    public function setMsg($msg) {
    	$this->msg = $msg;
    	return $this;
    }
    
    /**
     * 添加报文头部
     * @param string $key 名称
     * @param string $content 内容
     */
    public function addHeaders($key, $content) {
    	$this->headers[$key] = $content;
    }

    /** ------------------ 结果输出 ------------------ **/

    /**
     * 结果输出
     */
    public function output() {
    	$this->handleHeaders($this->headers);

        $rs = $this->getResult();

    	echo $this->formatResult($rs);
    }
    
    /** ------------------ getter ------------------ **/
    
    public function getResult() {
        $rs = array(
            'ret' => $this->ret,
            'data' => $this->data,
            'msg' => $this->msg,
        );

        return $rs;
    }

	/**
	 * 获取头部
	 * 
	 * @param string $key 头部的名称
	 * @return string/array 对应的内容，不存在时返回NULL，$key为NULL时返回全部
	 */
    public function getHeaders($key = NULL) {
        if ($key === NULL) {
            return $this->headers;
        }

        return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
    }

    /** ------------------ 内部方法 ------------------ **/

    protected function handleHeaders($headers) {
    	foreach ($headers as $key => $content) {
    		@header($key . ': ' . $content);
    	}
    }

    /**
     * 格式化需要输出返回的结果
     *
     * @param array $result 待返回的结果数据
     *
     * @see PhalApi_Response::getResult()
     */
    abstract protected function formatResult($result);
}
