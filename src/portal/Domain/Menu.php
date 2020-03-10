<?php
namespace Portal\Domain;

use Portal\Model\Menu as MenuModel;
use PhalApi\Exception\BadRequestException;

class Menu {

    public function getMenuInfo() {
        $model = new MenuModel();
        $menus = $model->getAllMenus();

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
        foreach ($menus as &$itRef) {
            $itRef['isMenu'] = 0;
            $itRef['checked'] = 1;
            $itRef['authority'] = $itRef['id'];
            $itRef['createTime'] = '';
            $itRef['updateTime'] = '';
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
        return $model->get($id);
    }
    
    public function updateMenu($id, $title, $href, $sort_num) {
        $model = new MenuModel();
        return $model->update($id, array('title' => $title, 'href' => $href, 'sort_num' => $sort_num));
    }
}
