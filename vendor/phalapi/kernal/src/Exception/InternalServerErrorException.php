<?php
namespace PhalApi\Exception;

use PhalApi\Exception;

/**
 * InternalServerErrorException 服务器运行异常错误
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-05
 */

class InternalServerErrorException extends Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            \PhalApi\T('Interal Server Error: {message}', array('message' => $message)), 500 + $code
        );
    }
}
