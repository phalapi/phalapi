<?php
namespace Portal\Domain;

use Portal\Model\Menu as MenuModel;
use PhalApi\Exception\BadRequestException;

class Menu {

    public function getMenuInfo() {
        $model = new MenuModel();
        $menus = $this->filterUnassignMenus($model->getAllMenus());

        // 第一层
        $menuInfo = $this->getByParentId($menus);

        // 第二层
        foreach ($menuInfo as &$itRef) {
            $menuInfoLv2 = $this->getByParentId($menus, $itRef['id']);
            if (!empty($menuInfoLv2)) {

                // 第三层
                foreach ($menuInfoLv2 as &$itRefLv2) {
                    $menuInfoLv3 = $this->getByParentId($menus, $itRefLv2['id']);

                    if (!empty($menuInfoLv3)) {
                        
                        // 第四层 盗梦空间
                        foreach ($menuInfoLv3 as &$itRefLv3) {
                            $menuInfoLv4 = $this->getByParentId($menus, $itRefLv3['id']);
                            
                            if (!empty($menuInfoLv4)) {
                                $itRefLv3['child'] = $menuInfoLv4;
                            }
                            
                        }
                        
                        // 第三层
                        $itRefLv2['child'] = $menuInfoLv3;
                    }
                } 

                // 第二层
                $itRef['child'] = $menuInfoLv2;
            }
        }

        return $menuInfo;
    }

    protected function getByParentId(&$menus, $parentId = 0) {
        $needMenus = array();
        foreach ($menus as $key => $it) {
            if ($it['parent_id'] == $parentId) {
                $needMenus[] = $it;
                unset($menus[$key]);
            }
        }

        return $needMenus;
    }
    
    public function listAllMenus() {
        $model = new MenuModel();
        $menus = $model->listAllMenus();
        
        $menus = $this->filterUnassignMenus($menus);
        
        foreach ($menus as &$itRef) {
            $itRef['isMenu'] = 0;
            $itRef['checked'] = 1;
            $itRef['authority'] = $itRef['id'];
            $itRef['createTime'] = '';
            $itRef['updateTime'] = '';
            
            unset($itRef['assign_admin_roles'], $itRef['assgin_admin_usernames']);
        }
        return $menus;
    }
    
    public function addNewMenu($title, $parent_id, $id, $href, $sort_num, $target) {
        
        $newData = array(
            'title' => $title,
            'parent_id' => $parent_id,
            'href' => $href,
            'sort_num' => $sort_num,
            'target' => $target,
        );
        
        if ($id > 0) {
            $newData['id'] = $id;
        }
        
        try {
            $model = new MenuModel();
            $id = $model->insert($newData);
            return intval($id);
        } catch (\PDOException $ex) {
            throw new BadRequestException($ex->getMessage());
        }
    }
    
    public function deleteMenu($id) {
        $model = new MenuModel();
        return $model->delete($id);
    }
    
    public function getMenu($id) {
        $model = new MenuModel();
        $menu = $model->get($id);
        
        if ($menu) {
            $menu['assgin_admin_usernames'] = strval($menu['assgin_admin_usernames']);
            
            // 
            $assignRoles = explode('|', $menu['assign_admin_roles']);
            $domainAdmin = new Admin();
            $roles = $domainAdmin->getAdminRoles();
            foreach ($roles as &$itRef) {
                $itRef['on'] = $itRef['role'] == 'super' || in_array($itRef['role'], $assignRoles) ? true : false;
            }
            
            $menu['assign_admin_roles'] = $roles;
        }
        
        return $menu;
    }
    
    public function updateMenu($id, $title, $href, $sort_num, $assign_admin_roles = array(), $assgin_admin_usernames = '') {
        $model = new MenuModel();
        $updateData = array(
            'title' => $title, 
            'href' => $href, 
            'sort_num' => $sort_num, 
            'assign_admin_roles' => implode('|', $assign_admin_roles),
            'assgin_admin_usernames' => trim($assgin_admin_usernames),
        );
        return $model->update($id, $updateData);
    }
    
    // 过滤未授权的菜单
    protected function filterUnassignMenus($menus) {
        $role = \PhalApi\DI()->admin->role;
        $username = \PhalApi\DI()->admin->username;
        if ($role == 'super') {
            return $menus;
        }
        $filmenus=array();
        foreach ($menus as $key => $it) {
            $it['assign_admin_roles'] = explode('|', $it['assign_admin_roles']);
            $it['assgin_admin_usernames'] = explode('|', $it['assgin_admin_usernames']);
            if (in_array($role, $it['assign_admin_roles']) || in_array($username, $it['assgin_admin_usernames'])) {
				array_push($filmenus,$it);
            }
        }
        
        return $filmenus;
		
    }
}
