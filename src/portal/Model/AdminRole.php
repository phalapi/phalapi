<?php
namespace Portal\Model;

use PhalApi\Model\DataModel;

class AdminRole extends DataModel {

    public function getTableName($id) {
        return 'phalapi_portal_admin_role';
    }
}
