<?php
namespace App\Common\User;

use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;

/**
 * 用户插件-用户服务
 * @author dogstar 20200331
 *
 */
class User {
    protected $id = 0;
    protected $profile = array();
    
    public function __construct() {
        $user_id = \PhalApi\DI()->request->get('user_id');
        $user_id = intval($user_id);
        $token = \PhalApi\DI()->request->get('token');
        
        if ($user_id && $token) {
            $domain = new UserSessionDomain();
            $is_login = $domain->checkSession($user_id, $token);
            
            if ($is_login) {
                $this->login($user_id);
            }
        }
    }
    
    // 登录用户
    public function login($user_id) {
        $userDomain = new UserDomain();
        $profile = $userDomain->getUserInfo($user_id, 'id,username,nickname,reg_time,avatar,mobile,sex,email');
        $this->profile = $profile ? $profile : $this->profile;
        $this->id = $user_id;
    }
    
    // 是否已登录
    public function isLogin() {
        return $this->id > 0 ? true : false;
    }
    
    // 获取用户ID
    public function getUserId() {
        return $this->id;
    }
    
    // 获取个人资料
    public function getProfile() {
        return $this->profile;
    }
    
    // 获取指定字段
    public function getProfileBy($filed, $default = NULL) {
        return isset($this->profile[$filed]) ? $this->profile[$filed] : $default;
    }
    
    // 获取资料
    public function __get($name) {
        return isset($this->profile[$name]) ? $this->profile[$name] : NULL;
    }
}
