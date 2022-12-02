<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 接口示例
 * @author dogstar 20220615
 */
class Docs extends Api {

    /**
     * 文档示例 - 接口文档使用示例
     * @desc 接口说明，更多使用请参考官方文档<a target="_blank" href="http://docs.phalapi.net/#/v2.0/api-docs">http://docs.phalapi.net/#/v2.0/api-docs</a>
     * @method GET
     * @version 1.0
     * @return string content
     * @exception 406 未授权或权限不足
     */
    public function usage() {
        return array('content' => '演示返回字段');
    }
}

