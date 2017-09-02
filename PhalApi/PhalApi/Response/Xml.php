<?php
/**
 * PhalApi_Response_Xml Xml响应类
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-15
 */

class PhalApi_Response_XML extends PhalApi_Response {

    public function __construct() {
        $this->addHeaders('Content-Type', 'text/html;charset=utf-8');
    }

    protected function formatResult($result) {
        return PhalApi_Tool::arrayToXml($result);
    }
}
