<?php
namespace PhalApi;

/**
 * Logger 日记抽象类
 *
 * - 对系统的各种情况进行纪录，具体存储媒介由实现类定义
 * - 日志分类型，不分优先级，多种类型可按并组合
 *
 * <br>接口实现示例：<br>
```
 *      class LoggerMock extends Logger {
 *          public function log($type, $msg, $data) {
 *              //nothing to do here ...
 *          }
 *      }
 *
 *      //保存全部类型的日记
 *      $logger = new LoggerMock(
 *          Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
 *
 *      //开发调试使用，且带更多信息
 *      $logger->debug('this is bebug test', array('name' => 'mock', 'ver' => '1.0.0'));
 *
 *      //业务场景使用
 *      $logger->info('this is info test', 'and more detail here ...');
 *
 *      //一些不该发生的事情
 *      $logger->error('this is error test');
```
 * 
 * @package PhalApi\Logger
 * @link http://www.php-fig.org/psr/psr-3/ Logger Interface
 * @license http://www.phalapi.net/license GPL 协议
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

abstract class Logger {

	/**
	 * @var int $logLevel 多个日记级别
	 */
    protected $logLevel = 0;

    /**
     * @var int LOG_LEVEL_DEBUG 调试级别
     */
    const LOG_LEVEL_DEBUG = 1;
    
    /**
     * @var int LOG_LEVEL_INFO 产品级别
     */
    const LOG_LEVEL_INFO = 2;
    
    /**
     * @var int LOG_LEVEL_ERROR 错误级别
     */
    const LOG_LEVEL_ERROR = 4;

    public function __construct($level) {
        $this->logLevel = $level;
    }

    /**
     * 日记纪录
     *
     * 可根据不同需要，将日记写入不同的媒介
     *
     * @param string $type 日记类型，如：info/debug/error, etc
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    abstract public function log($type, $msg, $data);

    /**
     * 应用产品级日记
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    public function info($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_INFO)) {
            return;
        }

        $this->log('info', $msg, $data);
    }

    /**
     * 开发调试级日记
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    public function debug($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_DEBUG)) {
            return;
        }

        $this->log('debug', $msg, $data);
    }

    /**
     * 系统错误级日记
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    public function error($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_ERROR)) {
            return;
        }

        $this->log('error', $msg, $data);
    }

    /**
     * 是否允许写入日记，或运算
     * @param int $logLevel
     * @return boolean
     */
    protected function isAllowToLog($logLevel) {
        return (($this->logLevel & $logLevel) != 0) ? TRUE : FALSE;
    }
}
