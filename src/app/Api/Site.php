<?php
namespace App\Api;
use PhalApi\Api;

/**
 * 首页
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 * @ignore
 */
class Site extends Api {
    public function getRules() {
        return array(
            'index' => array(
                'username'  => array('name' => 'username', 'default' => 'PhalApi', 'desc' => '用户名'),
            ),
        );
    }

    /**
     * 默认接口服务 <span class="ui label green small">默认</span>
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function index() {
        // 切换为默认的首页 @dogstar 20221112
        $projectName = \PhalApi\T('PhalApi API Framework');
        include API_ROOT . '/src/view/site/index.php';
        exit(0);

        return array(
            'title' => 'Hello ' . $this->username,
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        );
    }
}
