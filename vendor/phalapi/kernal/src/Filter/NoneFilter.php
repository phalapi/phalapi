<?php
namespace PhalApi\Filter;

use PhalApi\Filter;

/**
 * NoneFilter 无作为的拦截器
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-10-23
 */

class NoneFilter implements Filter {

    public function check() {
        // nothing here ...
    }
}
