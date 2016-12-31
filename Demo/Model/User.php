<?php

class Model_User extends PhalApi_Model_NotORM {

    public function getByUserId($userId) {
        return $this->getORM()
            ->select('*')
            ->where('id = ?', $userId)
            ->fetch();
    }

    public function getByUserIdWithCache($userId) {
        $key = 'userbaseinfo_' . $userId;
        $rs = DI()->cache->get($key);
        if ($rs === NULL) {
            $rs = $this->getByUserId($userId);
            DI()->cache->set($key, $rs, 600);
        }
        return $rs;
    }

    /**
    protected function getTableName($id) {
        return 'user';
    }
    */
}
