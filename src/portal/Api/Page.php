<?php
namespace Portal\Api;

use Portal\Common\Api;
use Portal\Domain\Menu as MenuDomain;

/**
 * 运营平台接口
 */
class Page extends Api {

    public function getRules() {
        return array(
            'addNewMenu' => array(
                'title' => array('name' => 'title', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '菜单标题'),
                'parent_id' => array('name' => 'parent_id', 'type' => 'int', 'min' => 0, 'desc' => '父菜单ID'),
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 0, 'desc' => '菜单ID'),
                'href' => array('name' => 'href', 'default' => '', 'desc' => '菜单ID'),
                'sort_num' => array('name' => 'sort_num', 'type' => 'int', 'desc' => '排序'),
                'target' => array('name' => 'target', 'desc' => '打开位置'),
            ),
            'deleteMenu' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'min' => 0, 'desc' => '菜单ID'),
            ),
            'getMenu' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'min' => 0, 'desc' => '菜单ID'),
            ),
            'updateMenu' => array(
                'title' => array('name' => 'title', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '菜单标题'),
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'min' => 0, 'desc' => '菜单ID'),
                'href' => array('name' => 'href', 'default' => '', 'desc' => '菜单ID'),
                'sort_num' => array('name' => 'sort_num', 'type' => 'int', 'desc' => '排序'),
            ),
        );
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
    
    /**
     * 获取树状菜单
     * @desc 获取全部的菜单，以树状结构返回。当前最多支持4级菜单。
     */
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
    
    /**
     * 添加新菜单
     * @desc 添加一个新菜单
     */
    public function addNewMenu() {
        $domain = new MenuDomain();
        $id = $domain->addNewMenu($this->title, $this->parent_id, $this->id, $this->href, $this->sort_num, $this->target);
        return array('id' => $id);
    }
    
    /**
     * 删除菜单
     * @desc 根据菜单删除菜单
     */
    public function deleteMenu() {
        $domain = new MenuDomain();
        $domain->deleteMenu($this->id);
    }
    
    /**
     * 获取菜单
     * @desc 根据ID获取菜单
     */
    public function getMenu() {
        $domain = new MenuDomain();
        return array('menu' => $domain->getMenu($this->id));
    }
    
    /**
     * 修改菜单
     * @desc 修改菜单
     */
    public function updateMenu() {
        $domain = new MenuDomain();
        return array('is_updated' => $domain->updateMenu($this->id, $this->title, $this->href, $this->sort_num));
    }
}
