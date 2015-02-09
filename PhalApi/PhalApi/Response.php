<?php
/**
 * PhalApi_Response 响应类
 *
 * - 拥有各种结果返回状态 ，以及对返回结果 的格式化
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

abstract class PhalApi_Response {

    protected $ret = 200;
    protected $data = array();
    protected $msg = '';
    
    protected $headers = array();
    
    public function setRet($ret) {
    	$this->ret = $ret;
    	return $this;
    }
    
    public function setData($data) {
    	$this->data = $data;
    	return $this;
    }
    
    public function setMsg($msg) {
    	$this->msg = $msg;
    	return $this;
    }
    
    public function addHeaders($key, $content) {
    	$this->headers[$key] = $content;
    }
    
    public function getResult() {
        $rs = array(
            'ret' => $this->ret,
            'data' => $this->data,
            'msg' => $this->msg,
        );

        return $rs;
    }

    public function output() {
    	$this->handleHeaders($this->headers);

        $rs = $this->getResult();

    	echo $this->formatResult($rs);
    }

    /**
     * 格式化需要输出返回的结果
     */
    abstract protected function formatResult($result);
    
    protected function handleHeaders($headers) {
    	foreach ($headers as $key => $content) {
    		header($key . ': ' . $content);
    	}
    }
}
