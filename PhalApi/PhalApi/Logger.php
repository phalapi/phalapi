<?php
/**
 * PhalApi_Logger 日记抽象类
 *
 * - 对系统的各种情况进行纪录，具体存储媒介由实现类定义
 * - 日志分类型，不分优先级，多种类型可按并组合
 *
 *      class PhalApi_Logger_Mock extends PhalApi_Logger {
 *          public function log($type, $msg, $data) {
 *              //nothing to do here ...
 *          }
 *      }
 *
 *      //保存全部类型的日记
 *      $logger = new PhalApi_Logger_Mock(
 *          PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);
 *
 *      //开发调试使用，且带更多信息
 *      $logger->debug('this is bebug test', array('name' => 'mock', 'ver' => '1.0.0'));
 *
 *      //业务场景使用
 *      $logger->info('this is info test', 'and more detail here ...');
 *
 *      //一些不该发生的事情
 *      $logger->error('this is error test');
 *
 * @author dogstar 2014-10-02
 */

abstract class PhalApi_Logger {

    protected $logLevel = 0;

    const LOG_LEVEL_DEBUG = 1;
    const LOG_LEVEL_INFO = 2;
    const LOG_LEVEL_ERROR = 4;

    public function __construct($level) {
        $this->logLevel = $level;
    }

    abstract public function log($type, $msg, $data);

    /**
     * 产品应用级日记
     */
    public function info($msg, $data = null) {
        if (($this->logLevel & self::LOG_LEVEL_INFO) == 0) {
            return;
        }

        $this->log('info', $msg, $data);
    }

    /**
     * 开发调试级日记
     */
    public function debug($msg, $data = null) {
        if (($this->logLevel & self::LOG_LEVEL_DEBUG) == 0) {
            return;
        }

        $this->log('debug', $msg, $data);
    }

    /**
     * 系统错误级日记
     */
    public function error($msg, $data = null) {
        if (($this->logLevel & self::LOG_LEVEL_ERROR) == 0) {
            return;
        }

        $this->log('error', $msg, $data);
    }
}
