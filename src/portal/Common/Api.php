<?php
namespace Portal\Common;

/**
 * 运营平台接口基类
 * @exception 406 管理员未登录
 * @author dogstar 20200307
 */
class Api extends \PhalApi\Api {

    protected function userCheck() {
        \PhalApi\DI()->admin->check();
    }
}
