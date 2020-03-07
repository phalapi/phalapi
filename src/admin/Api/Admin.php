<?php

namespace Admin\Api;

use PhalApi\Api;
use Admin\Domain\Admin as AdminDomain;

/**
 * 管理员模块
 * @author dogstar 20200307
 */
class Admin extends Api {
    
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'require' => true, 'desc' => '账号'),
                'password' => array('name' => 'password', 'require' => true, 'desc' => '密码'),
            ),
            'alterPassword'=> array(
                'oldPassword' => array('name' => 'old_password', 'require' => true, 'desc' => '旧密码'),
                'newPassword' => array('name' => 'new_password', 'require' => true, 'desc' => '新密码'),
            ),
        );
    }

    /**
     * 管理员登录
     * @desc 根据管理员账号和密码，进行登录
     */
    public function login() {
        $rs = array('is_login' => false);

        $domain = new AdminDomain();
        $rs['is_login'] = $domain->login($this->username, $this->password);
        
        return $rs;
    }

    /**
     * 修改密码
     * @desc 修改管理员自己的密码
     */
    public function alterPassword() {
        $rs = array('is_alter' => false);

        \PhalApi\DI()->admin->check();

        $domain = new AdminDomain();
        $rs['is_alter'] = $domain->alterPassword($this->oldPassword, $this->newPassword);

        return $rs;
    }

    /**
     * 退出管理员登录
     * @desc 退出当前管理的登录
     */
    public function logout() {
        \PhalApi\DI()->admin->logout();
        return array('is_logout' => true);
    }
}
