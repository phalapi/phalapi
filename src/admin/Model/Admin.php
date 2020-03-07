<?php
namespace Admin\Model;

use PhalApi\Model\NotORMModel;

class Admin extends NotORMModel {

    public function getTableName($id) {
        return 'phalapi_admin_admin';
    }

    public function getByUsername($username) {
        return $this->getORM()
            ->where('username', $username)
            ->fetchOne();
    }    
}
