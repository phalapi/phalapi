<?php
namespace Admin\Domain;

use Admin\Model\Menu as MenuModel;

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
                        $itRefLv2['child'] = $menuInfoLv3;
                    }
                } 

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
}
