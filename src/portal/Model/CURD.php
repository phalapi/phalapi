<?php
namespace Portal\Model;

use PhalApi\Model\DataModel;

class CURD extends DataModel {

    public function getTableName($id) {
        return 'phalapi_curd';
    }
}
