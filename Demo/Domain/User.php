<?php

class Domain_User {

    public function getBaseInfo($userId) {
        $rs = array();

        $userId = intval($userId);
        if ($userId <= 0) {
            return $rs;
        }

        $model = new Model_User();
        $rs = $model->getByUserId($userId);

        return $rs;
    }
}
