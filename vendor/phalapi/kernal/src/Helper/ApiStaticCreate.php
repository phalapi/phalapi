<?php
/**
 * Created by PhpStorm.
 * User: niebangheng
 * Date: 2019/1/26
 * Time: 14:53
 */

namespace PhalApi\Helper;


class ApiStaticCreate extends ApiList
{

    protected $webRoot = '';
    protected $theme = '';

    public function __construct($projectName, $theme = 'fold') {
        parent::__construct($projectName);
        $this->theme = $theme;
    }


    public function render($tplPath = NULL) {
        $theme = $this->theme;
        $trace = debug_backtrace();
        $listFilePath = $trace[0]['file'];
        $this->webRoot = substr($listFilePath, 0, strrpos($listFilePath, D_S));
        ob_start();
        // 运行模式
        parent::render($tplPath);
        $string = ob_get_clean();
        \PhalApi\Helper\saveHtml($this->webRoot, 'index', $string);
        $str = "
脚本执行完毕！离线文档保存路径为：
";
        $str .= $this->webRoot;
        echo $str . D_S . 'docs', PHP_EOL, PHP_EOL;

    }

    public function makeApiServiceLink($service, $theme = '') {
        ob_start();
        // 换一种更优雅的方式
        \PhalApi\DI()->request = new \PhalApi\Request(array('service' => $service));
        $apiDesc = new \PhalApi\Helper\ApiDesc($this->projectName);
        $apiDesc->render();

        $string = ob_get_clean();
        \PhalApi\Helper\saveHtml($this->webRoot, $service, $string);
        $link = $service . '.html';
        return $link;
    }

    public function getUri() {
        return '';
    }

    public function makeThemeButton($theme) {
        return '';
    }
}
