<?php
namespace Admin\Api;

use Admin\Common\Api;
use Admin\Domain\Menu as MenuDomain;

/**
 * 后台页面接口
 */
class Page extends Api {

    public function getRules() {
    }

    /**
     * 后台启动接口
     * @desc 进入后台首页时的初始化接口
     */
    public function startUp() {
        $homeInfo = array(
            'title' => '首页',
            'href' => 'page/welcome-1.html?t=1',
        );
        $logoInfo = array(
            'title' => 'PhalApi管理后台',
            'image' => 'images/logo.png',
            'href' => '/admin/',
        );

        $menuDomain = new MenuDomain();
        $menuInfo = $menuDomain->getMenuInfo();;

        return array('homeInfo' => $homeInfo, 'logoInfo' => $logoInfo, 'menuInfo' => $menuInfo);
    }
}
