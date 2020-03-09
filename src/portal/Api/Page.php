<?php
namespace Portal\Api;

use Portal\Common\Api;
use Portal\Domain\Menu as MenuDomain;

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
            'title' => 'PhalApi运营后台',
            'image' => 'images/logo.png',
            'href' => '/Portal/',
        );

        $menuDomain = new MenuDomain();
        $menuInfo = $menuDomain->getMenuInfo();
        
        $admin = array(
            'username' => \PhalApi\DI()->admin->username,
        );

        return array('homeInfo' => $homeInfo, 'logoInfo' => $logoInfo, 'menuInfo' => $menuInfo, 'admin' => $admin);
    }
    
    public function menu() {
        $menuDomain = new MenuDomain();
        $menus = $menuDomain->listAllMenus();
        $total = count($menus);
        
        header('content-type:application/json;charset=utf-8');
        $finalRs = array('code' => 0, 'msg' => '', 'count' => $total, 'data' => $menus);
        echo json_encode($finalRs);
        die();
        
        return array('menus' => $menus, 'total' => $total);
    }
}
