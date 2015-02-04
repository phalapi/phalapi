<?php
/**
 * PhalApi_Exception_BadRequest
 *
 * 客户端非法请求
 *
 * @author dogstar 2015-02-05
 */

class PhalApi_Exception_BadRequest extends PhalApi_Exception{

    public function __construct($message, $code = 0) {
        parent::__construct(
            T('Bad Request: {message}', array('message' => $message)), 400 + $code
        );
    }
}
