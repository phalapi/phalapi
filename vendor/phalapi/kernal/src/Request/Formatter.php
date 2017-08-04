<?php
namespace PhalApi\Request;

/**
 * Formatter 格式化接口
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */

interface Formatter {

    public function parse($value, $rule);
}
