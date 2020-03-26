<?php
namespace PhalApi\Error;

use PhalApi\Logger\FileLogger;

/**
 * 接口错误类
 *
 * @package     PhalApi\Error\ApiError
 * @license     http://www.phalapi.net/license GPL 协议 GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2020-03-25
 */
class ApiError implements \PhalApi\Error {

    protected $loggers = array();

    public function __construct() {
        // 注册错误处理
        set_error_handler(array($this, 'handleError'));

        // 注册致命错误时的处理
        register_shutdown_function(array($this, 'handleFatalPHPError'));
    }

    /**
     * 处理致命错误
     */
    public function handleFatalPHPError() {
        $last_error = error_get_last();
        if ($last_error) {
            $this->handleError($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }

    /**
     * 自定义的错误处理函数
     * @param int $errno 包含了错误的级别，是一个 integer
     * @param string $errstr 包含了错误的信息，是一个 string
     * @param string $errfile 可选的，包含了发生错误的文件名，是一个 string
     * @param int $errline 可选项，包含了错误发生的行号，是一个 integer
     * @link https://www.php.net/manual/zh/function.set-error-handler.php
     */
    public function handleError($errno, $errstr, $errfile = '', $errline = 0) {
        // if (!(error_reporting() & $errno)) {
        //    // This error code is not included in error_reporting, so let it fall
        //    // through to the standard PHP error handler
        //    return false;
        // }

        $error = 'Unknow';

        switch ($errno) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Error';
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_STRICT:
            $error = 'Strict';
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            break;
        default:
            break;
        }

        $context = array(
            'error' => $error,
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
            'time' => time(),
        );

        $this->reportError($context);

        return TRUE;
    }

    /**
     * 上报错误
     * @param array $context
     */
    protected function reportError($context) {
        $message = \PhalApi\T('{error} ({errno}): {errstr} in [File: {errfile}, Line: {errline}, Time: {time}]', $context);
        $this->getLogger($context['error'])->log($context['error'], $message, NULL);
    }

    /**
     * 根据不同错误，获取相应的日志服务，区分日志文件名前缀
     * @return \PhalApi\Logger\FileLogger
     */
    protected function getLogger($type) {
        if (!isset($this->loggers[$type])) {
            $config = \PhalApi\DI()->config->get('sys.file_logger');
            $config['file_prefix'] = lcfirst($type);
            $this->loggers[$type] = FileLogger::create($config);
        }

        return $this->loggers[$type];
    }

}

