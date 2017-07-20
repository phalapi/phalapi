<?php
namespace PhalApi\Exception;

use PhalApi\Exception;

/**
 * BadRequestException 客户端非法请求
 *
 * 客户端非法请求
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-05
 */

class BadRequestException extends Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            \PhalApi\T('Bad Request: {message}', array('message' => $message)), 400 + $code
        );
    }
}
