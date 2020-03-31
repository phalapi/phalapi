<?php

namespace App\Domain\User;
use App\Model\User\User as UserModel;

/**
 * 用户
 *
 * - 可用于自动生成一个新用户
 *
 * @author dogstar 20200331
 */

class User {

    public function getUserByUsername($username, $select = '*') {
        $model = new UserModel();
        return $model->getDataByUsername($username, $select);
    }
    
    /**
     * 注册新用户
     *
     * @param string $username 账号
     * @param string $password 密码
     * @param array $moreInfo 更多注册信息，必须在数据库表中有此字段
     * @return int 用户id
     */
    public function register($username, $password, $moreInfo = array()) {
        $newUserInfo = $moreInfo;
        $newUserInfo['username'] = $username;

        $newUserInfo['salt'] = \PhalApi\Tool::createRandStr(32);
        $newUserInfo['password'] = $this->encryptPassword($password, $newUserInfo['salt']);
        $newUserInfo['reg_time'] = $_SERVER['REQUEST_TIME'];

        $userModel = new UserModel();
        $id = $userModel->insert($newUserInfo);
        
        return intval($id);
    }
    
    // 账号登录
    public function login($username, $password) {
        $user = $this->getUserByUsername($username, 'id,password,salt');
        if (!$user) {
            return false;
        }
        
        $encryptPassword = $this->encryptPassword($password, $user['salt']);
        if ($encryptPassword !== $user['password']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取用户信息
     * @param unknown $userId
     * @return array|unknown
     */
    public function getUserInfo($userId, $select = '*') {
        $rs = array();
        
        $userId = intval($userId);
        if ($userId <= 0) {
            return $rs;
        }
        
        $model = new UserModel();
        $rs = $model->get($userId, $select);
        
        if (empty($rs)) {
            return $rs;
        }
        
        $rs['id'] = intval($rs['id']);
        
        return $rs;
    }
    
    // 密码加密算法
    public function encryptPassword($password, $salt) {
        return md5(md5(\PhalApi\DI()->config->get('phalapi_user.common_salt')) . md5($password) . sha1($salt));
    }
    
}