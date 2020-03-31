<?php
namespace Portal\Model;

use PhalApi\Model\NotORMModel;

class Admin extends NotORMModel {

    public function getTableName($id) {
        return 'phalapi_portal_admin';
    }

    public function getByUsername($username) {
        return $this->getORM()
            ->where('username', $username)
            ->fetchOne();
    }
    
    public function getTotalNum() {
        return $this->getORM()
            ->count('id');
    }
}
