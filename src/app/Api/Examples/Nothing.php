<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 屏蔽的类
 * @ignore
 * @desc 主要用于说明，当使用了下面这个ignore注解时，则不会显示在接口列表文档上
 */
class Nothing extends Api {

    /**
     * 当在方法使用ingore注解时，则不会显示在接口列表文档上
     * @ignore
     */
    public function foo() {
    }
}
