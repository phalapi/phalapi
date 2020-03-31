<?php
namespace App\Api\User;

use PhalApi\Api;
use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;
use PhalApi\Exception\BadRequestException;

/**
 * 用户插件
 * @author dogstar 20200331
 */
class User extends Api {
    public function getRules() {
        return array(
            'register' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号，账号需要唯一'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
                'avatar' => array('name' => 'avatar', 'default' => '', 'max' => 500, 'desc' => '头像链接'),
                'sex' => array('name' => 'sex', 'type' => 'int', 'default' => 0, 'desc' => '性别，1男2女0未知'),
                'email' => array('name' => 'email', 'default' => '', 'max' => 50, 'desc' => '邮箱'),
                'mobile' => array('name' => 'mobile', 'default' => '', 'max' => 20, 'desc' => '手机号'),
            ),
            'login' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
            ),
            'checkSession' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'require' => true, 'desc' => '会话token'),
            ),
            'profile' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'require' => true, 'desc' => '会话token'),
            ),
        );
    }
    
    /**
     * 注册账号
     * @desc 注册一个新账号
     * @return int user_id 新账号的ID
     */
    public function register() {
        $domain = new UserDomain();
        $user = $domain->getUserByUsername($this->username, 'id');
        if ($user) {
            throw new BadRequestException($this->username . '账号已注册');
        }
        
        $moreInfo = array(
            'avatar' => $this->avatar,
            'sex' => $this->sex,
            'email' => $this->email,
            'mobile' => $this->mobile,
        );
        $userId = $domain->register($this->username, $this->password, $moreInfo);
        
        return array('user_id' => $userId);
    }
    
    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return boolean is_login 是否登录成功
     * @return int user_id 用户ID
     * @return string token 登录成功后的token,会话token
     * @return boolean is_login 是否登录成功
     */
    public function login() {
        $username = $this->username;   // 账号参数
        $password = $this->password;   // 密码参数
        
        $domain = new UserDomain();
        $user = $domain->getUserByUsername($this->username, 'id');
        if (!$user) {
            throw new BadRequestException($this->username . '账号不存在');
        }
        $user_id = intval($user['id']);
        
        $is_login = $domain->login($this->username, $this->password);
        $token = '';
        if ($is_login) {
            $session = new UserSessionDomain();
            $token = $session->generate($user_id);
        }
        
        return array('is_login' => $is_login, 'user_id' => $user_id, 'token' => $token);
    }
    
    /**
     * 检测登录状态
     * @desc 检测当前登录状态 
     */
    public function checkSession() {
        $user = \PhalApi\DI()->user;
        $is_login = $user->isLogin();
        return array('is_login' => $is_login);
    }
    
    /**
     * 获取我的个人信息
     * @desc 获取当前用户的个人信息
     */
    public function profile() {
        $user = \PhalApi\DI()->user;
        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期');
        }
        
        $profile = $user->getProfile();
        
        return array('profile' => $profile);
    }
} 
