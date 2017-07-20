<?php
namespace PhalApi\Exception;

use PhalApi\Exception;

/**
 * RedirectException 重定向
 *
 * 重定向，需要进一步的操作以完成请求
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-01
 */

class RedirectException extends Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            \PhalApi\T('Redirect: {message}', array('message' => $message)), 300 + $code
        );
    }
}
