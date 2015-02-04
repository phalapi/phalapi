<?php
/**
 * PhalApi_Response 响应类
 *
 * - 拥有各种结果返回状态 ，以及对返回结果 的格式化
 *
 * @author dogstar 2014-10-02
 */

class PhalApi_Response {

    private $ret = 200;
    private $data = array();
    private $msg = '';
    
    private $headers = array();
    
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
    
    public function output() {
    	$result = $this->formatResult();
    	
    	$this->handleHeaders($this->headers);

        echo $result;
    }
    
    protected function formatResult() {
        $result = array(
            'ret' => $this->ret,
            'data' => $this->data,
            'msg' => $this->msg,
        );

        return json_encode($result);
    }
    
    protected function handleHeaders($headers) {
    	foreach ($headers as $key => $content) {
    		header($key . ':' . $content);
    	}
    }
}
