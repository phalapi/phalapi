<?php
namespace App\Model\Examples;

use PhalApi\Model\DataModel;

class User extends DataModel {
    
    protected function getTableName($id) {
        return 'phalapi_user';
    }
}