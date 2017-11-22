<?php
namespace PhalApi\Helper;

/**
 * ApiOnline - 在线接口文档
 *     
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-11-22
 */

class ApiOnline {

    protected $projectName;

    public function __construct($projectName) {
        $this->projectName = $projectName;
    }

    /**
     * @param string $tplPath 模板绝对路径
     */
    public function render($tplPath = NULL) {
        header('Content-Type:text/html;charset=utf-8');
    }
}
