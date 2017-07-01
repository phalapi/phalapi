<?php
/**
 * PhalApi_Exception_Redirect 重定向
 *
 * 重定向，需要进一步的操作以完成请求
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-01
 */

class PhalApi_Exception_Redirect extends PhalApi_Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            T('Redirect: {message}', array('message' => $message)), 300 + $code
        );
    }
}
