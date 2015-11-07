<?php
/**
 * PhalApi_Request_Formatter 格式化接口
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */

interface PhalApi_Request_Formatter {

    public function parse($value, $rule);
}
