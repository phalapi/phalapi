<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 接口示例
 */
class Response extends Api {

    /**
     * 演示在返回结果根节点添加额外的字段返回
     * @desc 自定义动态返回JSON根节点，增加最外层返回消息，例如最外层的【status 状态码】和【time 当前系统时间】
     */
    public function topResult() {
        $di = \PhalApi\DI();
        // 支持连贯操作
        $di->response->addResult('status', 'OK')->addResult('time', time());

        return array('status' => 'data结构内的status 状态码');
    }
}
