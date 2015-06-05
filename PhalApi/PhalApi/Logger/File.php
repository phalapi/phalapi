<?php
/**
 * PhalApi_Logger_File 文件日记纪录类
 *
 * - 将日记写入文件，文件目录可以自定义
 * 
 * <br>使用示例：<br>
```
 *      //目录为./Runtime，且保存全部类型的日记
 *      $logger = new PhalApi_Logger_File('./Runtime', 
 * 	        PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);
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

class PhalApi_Logger_File extends PhalApi_Logger {

    protected $logFolder;
    protected $dateFormat;

    protected $logFile;

    public function __construct($logFolder, $level, $dateFormat = 'Y-m-d H:i:s') {
        $this->logFolder = $logFolder;
        $this->dateFormat = $dateFormat;

        parent::__construct($level);

        $this->init();
    }

    protected function init() {
        $folder = $this->logFolder 
            . DIRECTORY_SEPARATOR . 'log' 
            . DIRECTORY_SEPARATOR . date('Ym', $_SERVER['REQUEST_TIME']);
        
        if (!file_exists($folder)) {
            mkdir($folder . '/', 0777, TRUE);
        }

        $this->logFile = $folder 
            . DIRECTORY_SEPARATOR . date('Ymd', $_SERVER['REQUEST_TIME']) . '.log';

        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0777);
        }
    }

    public function log($type, $msg, $data) {
        $msgArr = array();
        $msgArr[] = date($this->dateFormat, $_SERVER['REQUEST_TIME']);
        $msgArr[] = strtoupper($type);
        $msgArr[] = str_replace(PHP_EOL, '\n', $msg);
        if ($data !== NULL) {
            $msgArr[] = is_array($data) ? json_encode($data) : $data;
        }

        $content = implode('|', $msgArr) . PHP_EOL;

        file_put_contents($this->logFile, $content, FILE_APPEND);
    }
}
