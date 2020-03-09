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
}

