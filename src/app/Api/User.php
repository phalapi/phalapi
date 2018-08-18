<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 用户模块接口服务
 */
class User extends Api {
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户名'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
            ),
        );
    }
    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return boolean is_login 是否登录成功
     * @return int user_id 用户ID
     */
    public function login() {
        $username = $this->username;   // 账号参数
        $password = $this->password;   // 密码参数
        // 更多其他操作……

        return array('is_login' => true, 'user_id' => 8);
    }
} 
