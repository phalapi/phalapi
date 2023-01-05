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

    protected $tplData = array();

    public function __construct($projectName) {
        $this->projectName = $projectName;

        $this->assign('projectName', $projectName);
    }

    /**
     * 赋值模板变量
     */
    public function assign($name, $value) {
        $this->tplData[$name] = $value;
        return $this;
    }

    /**
     * 获取模板变量
     */
    public function getTplData() {
        return $this->tplData;
    }

    /**
     * @param string $tplPath 模板绝对路径
     */
    public function render($tplPath = NULL) {
        if ($tplPath && file_exists($tplPath)) {
            header('Content-Type:text/html;charset=utf-8');

            extract($this->tplData);

            include $tplPath;
        }
    }
}
