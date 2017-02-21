<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * PhalApi_Exception_BadRequest 客户端非法请求
 *
 * 客户端非法请求
 *
 * @package     PhalApi\Exception
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-05
 */

class PhalApi_Exception_BadRequest extends PhalApi_Exception{

    public function __construct($message, $code = 0) {
        parent::__construct(
            T('Bad Request: {message}', array('message' => $message)), 400 + $code
        );
    }
}
