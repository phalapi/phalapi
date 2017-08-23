<?php
namespace PhalApi\Response;

use PhalApi\Response;

/**
 * JsonpResponse JSON响应类
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

class JsonpResponse extends Response {

    protected $callback = '';

    protected $options;

    /**
     * @param string $callback JS回调函数名
     */
    public function __construct($callback, $options = 0) {
        $this->callback = $this->clearXss($callback);
        $this->options = $options;

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
        echo $this->callback . '(' . json_encode($result, $this->options) . ')';
    }
}
