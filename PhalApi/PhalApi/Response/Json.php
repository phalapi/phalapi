<?php
/**
 * JSON响应类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

class PhalApi_Response_Json extends PhalApi_Response {

    public function __construct() {
    	$this->addHeaders('Content-Type', 'text/html;charset=utf-8');
    }
    
    protected function formatResult($result) {
        return json_encode($result);
    }
    
}
