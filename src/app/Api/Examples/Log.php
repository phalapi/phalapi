<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 接口示例
 */
class Log extends Api {

    /**
     * 日记 - 写入日志
     * @desc 演示日志操作，包括写系统异常类日志、业务纪录类日志、开发调试类日志
     */
    public function run() {
        // 系统异常类日志：只有描述
        \PhalApi\DI()->logger->error('fail to insert DB');

        // 系统异常类日志：描述 + 简单的信息
        \PhalApi\DI()->logger->error('fail to insert DB', 'try to register user dogstar');

        // 系统异常类日志：描述 + 当时的上下文数据
        $data = array('name' => 'dogstar', 'password' => '123456');
        \PhalApi\DI()->logger->error('fail to insert DB', $data);

        // 业务纪录类日志：假设：10 + 2 = 12
        \PhalApi\DI()->logger->info('add user exp', array('name' => 'dogstar', 'before' => 10, 'addExp' => 2, 'after' => 12, 'reason' => 'help one more phper'));

        // 开发调试类日志：只有描述
        \PhalApi\DI()->logger->debug('just for test');

        // 开发调试类日志：描述 + 简单的信息
        \PhalApi\DI()->logger->debug('just for test', '一些其他的描述 ...');

        // 开发调试类日志：描述 + 当时的上下文数据
        \PhalApi\DI()->logger->debug('just for test', array('name' => 'dogstar', 'password' => '******'));

        // 开发调试类日志：更灵活的日志分类
        \PhalApi\DI()->logger->log('demo', 'add user exp', array('name' => 'dogstar', 'after' => 12));
        \PhalApi\DI()->logger->log('test', 'add user exp', array('name' => 'dogstar', 'after' => 12));
    }
}
