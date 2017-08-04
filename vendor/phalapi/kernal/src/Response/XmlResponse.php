<?php
namespace PhalApi\Response;

use PhalApi\Response;
use PhalApi\Tool;

/**
 * XmlResponse xml响应类
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-15
 */

class XmlResponse extends Response {

    public function __construct() {
        $this->addHeaders('Content-Type', 'text/html;charset=utf-8');
    }

    protected function formatResult($result) {
        return Tool::arrayToXml($result);
    }
}
