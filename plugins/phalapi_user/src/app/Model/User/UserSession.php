<?php

namespace App\Model\User;
use PhalApi\Model\NotORMModel as NotORM;

class UserSession extends NotORM {
    
    protected function getTableName($id) {
        return 'phalapi_user_session';
    }
    
    /**
     * 带缓存的获取
     */
    public function getExpiresTime($userId, $token) {
        $mcKey = $this->getExpiresTimeMcKey($userId, $token);
        $expiresTime = NULL;
        $cache = \Phalapi\DI()->cache;

        if (isset($cache)) {
            $expiresTime = $cache->get($mcKey);
        }

        if ($expiresTime === NULL) {
            $expiresTime = 0;
            $row = $this->getORM($userId)
                ->select('expires_time')
                ->where('user_id', $userId)
                ->where('token', $token)
                ->fetch();
            if (!empty($row)) {
                $expiresTime = intval($row['expires_time']);
            }

            if (isset($cache)) {
                $cache->set($mcKey, $expiresTime, 3600);
            }
        }

        return $expiresTime;
    }

    public function updateExpiresTime($userId, $token, $newExpiresTime) {
        $mcKey = $this->getExpiresTimeMcKey($userId, $token);
        $cache = DI()->cache;

        if (isset($cache)) {
            $cache->set($mcKey, $newExpiresTime, 3600);
        }

        $row = $this->getORM($userId)
            ->select('times')
            ->where('user_id = ?', $userId)
            ->where('token = ?', $token)
            ->fetch();

        if (empty($row)) {
            return;
        }

        $data = array();
        $data['expires_time'] = $newExpiresTime;
        $data['times'] = $row['times'] + 1;

        $this->getORM($userId)
            ->where('user_id = ?', $userId)
            ->where('token = ?', $token)
            ->update($data);
    }

    protected function getExpiresTimeMcKey($userId, $token) {
        return sprintf('%s:%s:user_user_session:expires_time', $userId, $token);
    }
}