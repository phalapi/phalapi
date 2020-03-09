<?php
namespace Portal\Model;

use PhalApi\Model\NotORMModel;

class Menu extends NotORMModel {

    public function getTableName($id) {
        return 'phalapi_portal_menu';
    }

    public function getAllMenus() {
        return $this->getORM()
            ->order('sort_num')
            ->fetchAll();
    }    
    
    public function listAllMenus() {
        return $this->getORM()
        ->select('id, id as authorityId, title as authorityName, sort_num as orderNumber, parent_id as parentId, icon as menuIcon, href as menuUrl')
        ->order('parent_id, sort_num')
        ->fetchAll();
    }
}

