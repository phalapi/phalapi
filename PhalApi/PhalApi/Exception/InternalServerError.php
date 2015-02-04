<?php
/**
 * PhalApi_Exception_InternalServerError 服务器运行异常错误
 *
 * @author dogstar 2015-02-05
 */

class PhalApi_Exception_InternalServerError extends PhalApi_Exception {

    public function __construct($message, $code = 0) {
        parent::__construct(
            T('Interal Server Error: {message}', array('message' => $message)), 500 + $code
        );
    }
}
