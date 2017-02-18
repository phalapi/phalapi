<?php

class Domain_User {

    public function getBaseInfo($userId) {
        $rs = array();

        $userId = intval($userId);
        if ($userId <= 0) {
            return $rs;
        }

		// Version 1: Simple retrive
        $model = new Model_User();
        $rs = $model->getByUserId($userId);

		// Version 2: Use single point cache/multi level cache which implements in Model
		/**
        $model = new Model_User();
        $rs = $model->getByUserIdWithCache($userId);
		*/

		// Version 3: Cache + Proxy
		/**
		$query = new PhalApi_ModelQuery();
		$query->id = $userId;
		$modelProxy = new ModelProxy_UserBaseInfo();
		$rs = $modelProxy->getData($query);
		*/

        return $rs;
    }
}
