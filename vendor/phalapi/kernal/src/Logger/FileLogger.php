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
    protected $filePrefix;
    protected $separator;

    protected $isJsonUU = FALSE; // 是否JSON保持中文显示，PHP 5.4.0版本以上方可支持JSON_UNESCAPED_UNICODE

    /**
     * 构造函数
     * @param string $logFolder 日记目录，需要使用已存在且有写入权限的绝对目录路径
     * @param int $level 需要纪录的日记级别，如：Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR
     * @param string $dateFormat 时间日期格式，默认是：Y-m-d H:i:s
     * @param boolean $debug 是否调试，默认与DI的调试保持一致
     * @param string $filePrefix 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件名
     * @param string $separator 日记内容分隔符
     */
    public function __construct($logFolder, $level, $dateFormat = 'Y-m-d H:i:s', $debug = NULL, $filePrefix = '', $separator = '|') {
        $this->logFolder    = $logFolder;
        $this->dateFormat   = $dateFormat;
        $this->debug        = $debug !== NULL ? $debug : \PhalApi\DI()->debug;
        $this->isJsonUU     = version_compare(PHP_VERSION, '5.4.0' , '>=') ? TRUE : FALSE;
        $this->separator    = $separator;
        $this->setFilePrefix($filePrefix);

        parent::__construct($level);
        
        $this->init();
    }

    /**
     * 根据配置数组创建实例，配置参数与构建参数列表一一对应
     * @param string $config['log_folder'] 日记目录，需要使用已存在且有写入权限的绝对目录路径，默认为：API_ROOT . '/runtime'
     * @param int $config['level'] 需要纪录的日记级别，默认：Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR
     * @param string $config['date_format'] 时间日期格式，默认是：Y-m-d H:i:s
     * @param boolean $config['debug'] 是否调试，默认与DI的调试保持一致
     * @param string $config['file_prefix'] 文件名前缀，必须为有效的文件名组成部分，自动使用下划线连接系统文件名
     * @param string $config['separator'] 日记内容分隔符
     */
    public static function create($config) {
        $logFolder  = isset($config['log_folder'])  ? $config['log_folder']  : API_ROOT . '/runtime';
        $level      = isset($config['level'])       ? $config['level']       : Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR;
        $dateFormat = isset($config['date_format']) ? $config['date_format'] : 'Y-m-d H:i:s';
        $debug      = isset($config['debug'])       ? $config['debug']       : NULL;
        $filePrefix = isset($config['file_prefix']) ? $config['file_prefix'] : '';
        $separator  = isset($config['separator'])   ? $config['separator']   : '|';

        return new static($logFolder, $level, $dateFormat, $debug, $filePrefix, $separator);
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
                // 调试时，显示warning提示
                mkdir($folder . '/', 0777, TRUE);
            } else {
                // 静默创建
                @mkdir($folder . '/', 0777, TRUE);
            }
        }

        // 每天一个文件
        $this->logFile = $folder
            . DIRECTORY_SEPARATOR . $this->filePrefix . $this->fileDate . '.log';
        if (!file_exists($this->logFile)) {
            // 当没有权限时，touch会抛出(Permission denied)异常
            @touch($this->logFile);
            // touch失败时，chmod会抛出(No such file or directory)异常
            if (file_exists($this->logFile)) {
                chmod($this->logFile, 0777);
            }
        }
    }

    // 切换日记文件名前缀，注意切换后全部的日记将写入到此文件前缀日记文件！
    public function switchFilePrefix($filePrefix) {
        $this->setFilePrefix($filePrefix);

        // 重置已经初始化的文件，重新检测并创建
        $this->fileDate = '';
        $this->init();

        return $this;
    }

    protected function setFilePrefix($filePrefix) {
        $this->filePrefix  = !empty($filePrefix) ? rtrim(strval($filePrefix), '_') . '_' : '';
    }

    public function log($type, $msg, $data) {
        $this->init();

        $msgArr = array();
        $msgArr[] = date($this->dateFormat, time());
        $msgArr[] = strtoupper($type);
        $msgArr[] = str_replace(PHP_EOL, '\n', $msg);
        if ($data !== NULL) {
            $msgArr[] = is_array($data) 
                ? ($this->isJsonUU ? json_encode($data, JSON_UNESCAPED_UNICODE) : json_encode($data))
                : $data;
        }

        $content = implode($this->separator, $msgArr) . PHP_EOL;

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
