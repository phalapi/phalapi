<?php
namespace PhalApi;

/**
 * 错误类
 *
 * @package     PhalApi\Error
 * @license     http://www.phalapi.net/license GPL 协议 GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2020-03-25
 */
interface Error {

    /**
     * 自定义的错误处理函数
     * @param int $errno 包含了错误的级别，是一个 integer
     * @param string $errstr 包含了错误的信息，是一个 string
     * @param string $errfile 可选的，包含了发生错误的文件名，是一个 string
     * @param int $errline 可选项，包含了错误发生的行号，是一个 integer
     */
   public function handleError($errno, $errstr, $errfile = '', $errline = 0); 
}
