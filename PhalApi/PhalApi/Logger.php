<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * Logger Class
 *
 * - Record various cases of the project, storage depends upon the implementation of sub-class
 * - There is no priority for logger level, and they can combine with each other
 *
 * <br>Implementation and usage:<br>
```
 *      class PhalApi_Logger_Mock extends PhalApi_Logger {
 *          public function log($type, $msg, $data) {
 *              // nothing to do here ...
 *          }
 *      }
 *
 *      // save all kinds of the logs
 *      $logger = new PhalApi_Logger_Mock(
 *          PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);
 *
 *      // this is for developers, with more detail
 *      $logger->debug('this is bebug test', array('name' => 'mock', 'ver' => '1.0.0'));
 *
 *      // this is for business
 *      $logger->info('this is info test', 'and more detail here ...');
 *
 *      // something should not happen
 *      $logger->error('this is error test');
```
 * 
 * @package     PhalApi\Logger
 * @link        http://www.php-fig.org/psr/psr-3/ Logger Interface
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

abstract class PhalApi_Logger {

    /**
     * @var     int     $logLevel           log level, can be multi
     */
    protected $logLevel = 0;

    /**
     * @var     int     LOG_LEVEL_DEBUG     debug level
     */
    const LOG_LEVEL_DEBUG = 1;

    /**
     * @var     int     LOG_LEVEL_INFO      prodution level
     */
    const LOG_LEVEL_INFO = 2;

    /**
     * @var     int     LOG_LEVEL_ERROR     error level
     */
    const LOG_LEVEL_ERROR = 4;

    public function __construct($level) {
        $this->logLevel = $level;
    }

    /**
     * Record log
     *
     * wtite the log into different storage medium according your need
     *
     * @param   string          $type       the type of log, such as info, debug, error, etc.
     * @param   string          $msg        key description of log
     * @param   string/array    $data       infomation of the context
     * @return  NULL
     */
    abstract public function log($type, $msg, $data);

    /**
     * The logs in production level
     * 
     * @param   string          $msg        key description of log
     * @param   string/array    $data       infomation of the context
     * @return  NULL
     */
    public function info($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_INFO)) {
            return;
        }

        $this->log('info', $msg, $data);
    }

    /**
     * The logs in debugger level
     * 
     * @param   string          $msg        key description of log
     * @param   string/array    $data       infomation of the context
     * @return  NULL
     */
    public function debug($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_DEBUG)) {
            return;
        }

        $this->log('debug', $msg, $data);
    }

    /**
     * The logs in system error level
     * 
     * @param   string          $msg        key description of log
     * @param   string/array    $data       infomation of the context
     * @return  NULL
     */
    public function error($msg, $data = NULL) {
        if (!$this->isAllowToLog(static::LOG_LEVEL_ERROR)) {
            return;
        }

        $this->log('error', $msg, $data);
    }

    /**
     * Is allowed to write into or not, arithmetic or operation
     * 
     * @param       int         $logLevel
     * @return      boolean
     */
    protected function isAllowToLog($logLevel) {
        return (($this->logLevel & $logLevel) != 0) ? TRUE : FALSE;
    }
}
