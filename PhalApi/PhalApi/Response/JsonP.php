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
 * PhalApi_Response_JsonP JSON响应类
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

class PhalApi_Response_JsonP extends PhalApi_Response {

    protected $callback = '';

    /**
     * @param string $callback JS回调函数名
     */
    public function __construct($callback) {
        $this->callback = $this->clearXss($callback);

        $this->addHeaders('Content-Type', 'text/javascript; charset=utf-8');
    }

    /**
     * 对回调函数进行跨站清除处理
     *
     * - 可使用白名单或者黑名单方式处理，由接口开发再实现
     */
    protected function clearXss($callback) {
        return $callback;
    }

    protected function formatResult($result) {
        echo $this->callback . '(' . json_encode($result) . ')';
    }
}
