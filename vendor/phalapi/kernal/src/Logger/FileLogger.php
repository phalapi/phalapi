<?php
namespace PhalApi\Logger;

use PhalApi\Logger;
use PhalApi\Tool;
use PhalApi\Exception\InternalServerErrorException;

/**
 * FileLogger 文件日记纪录类
 *
 * - 将日记写入文件，文件目录可以自定义
 *
 * <br>使用示例：<br>
```
 *      //目录为./Runtime，且保存全部类型的日记
 *      $logger = new FileLogger('./Runtime',
 * 	        Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
 *
 *      //日记会保存在在./Runtime/debug_log/目录下
 *      $logger->debug('this is bebug test');
```
 *
 * @package     PhalApi\Logger
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class FileLogger extends Logger {

    /** 外部传参 **/
    protected $logFolder;
    protected $dateFormat;

    /** 内部状态 **/
    protected $fileDate;
    protected $logFile;
    protected $debug = FALSE;

    public function __construct($logFolder, $level, $dateFormat = 'Y-m-d H:i:s', $debug = NULL) {
        $this->logFolder = $logFolder;
        $this->dateFormat = $dateFormat;
        $this->debug = $debug !== NULL ? $debug : \PhalApi\DI()->debug;

        parent::__construct($level);
        
        $this->init();
    }

    protected function init() {
        // 跨天时新建日记文件
        $curFileDate = date('Ymd', time());
        if ($this->fileDate == $curFileDate) {
            return;
        }
        $this->fileDate = $curFileDate;

        // 每月一个目录
        $folder = $this->logFolder
            . DIRECTORY_SEPARATOR . 'log'
            . DIRECTORY_SEPARATOR . substr($this->fileDate, 0, -2);
        if (!file_exists($folder)) {
            if ($this->debug) {
                // 调试时，显示创建，更友好的提示
                if (!is_writeable($this->logFolder)) {
                    throw new InternalServerErrorException(\PhalAPi\T('Failed to log into file, because permission denied: {path}', array('path' => Tool::getAbsolutePath($folder))));
                }
                mkdir($folder . '/', 0777, TRUE);
            } else {
                // 静默创建
                @mkdir($folder . '/', 0777, TRUE);
            }
        }

        // 每天一个文件
        $this->logFile = $folder
            . DIRECTORY_SEPARATOR . $this->fileDate . '.log';
        if (!file_exists($this->logFile)) {
            // 当没有权限时，touch会抛出(Permission denied)异常
            @touch($this->logFile);
            // touch失败时，chmod会抛出(No such file or directory)异常
            if (file_exists($this->logFile)) {
                chmod($this->logFile, 0777);
            }
        }
    }

    public function log($type, $msg, $data) {
        $this->init();

        $msgArr = array();
        $msgArr[] = date($this->dateFormat, time());
        $msgArr[] = strtoupper($type);
        $msgArr[] = str_replace(PHP_EOL, '\n', $msg);
        if ($data !== NULL) {
            $isGreaterThan540 = version_compare(PHP_VERSION, '5.4.0' , '>=');
            $msgArr[] = is_array($data) 
                ? ($isGreaterThan540 ? json_encode($data, JSON_UNESCAPED_UNICODE) : json_encode($data))
                : $data;
        }

        $content = implode('|', $msgArr) . PHP_EOL;

        if ($this->debug) {
            // 调试时，显示创建，更友好的提示
            if (!is_writeable($this->logFile)) {
                throw new InternalServerErrorException(\PhalAPi\T('Failed to log into file, because permission denied: {path}', array('path' => Tool::getAbsolutePath($this->logFile))));
            }
            file_put_contents($this->logFile, $content, FILE_APPEND);
        } else {
            // 静默写入
            @file_put_contents($this->logFile, $content, FILE_APPEND);
        }
    }
}
