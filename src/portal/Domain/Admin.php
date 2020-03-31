<?php
namespace Portal\Domain;

use Portal\Model\Admin as AdminModel;
use Portal\Model\AdminRole as AdminRoleModel;

class Admin {

    const ADMIN_STATE_NORMAL = 1;
    const ADMIN_STATE_BANNED = 0;

    const ADMIN_ROLE_NORMAL = 'admin';
    const ADMIN_ROLE_SUPERMAN = 'super';

    public function login($username, $password) {
        $model = new AdminModel();
        $admin = $model->getByUsername($username);

        if (empty($admin)) {
            return false;
        }

        if ($admin['state'] != self::ADMIN_STATE_NORMAL) {
            return false;
        }

        $encryptPass = $this->encryptPassword($password, $admin['salt']);
        if ($encryptPass != $admin['password']) {
            return false;
        }

        // 开启会话
        \PhalApi\DI()->admin->login($admin['id'], $username, $admin['role']);

        return true;
    }    

    public function alterPassword($oldPassword, $newPassword) {
        $model = new AdminModel();
        $admin = $model->get(\PhalApi\DI()->admin->id);

        if (empty($admin)) {
            return false;
        }

        $encryptPass = $this->encryptPassword($oldPassword, $admin['salt']);
        if ($encryptPass != $admin['password']) {
            return false;
        }

        $model->update(\PhalApi\DI()->admin->id, array('password' => $this->encryptPassword($newPassword, $admin['salt'])));

        \PhalApi\DI()->admin->logout();

        return true;
    }

    public function encryptPassword($password, $salt) {
        return md5(md5(sha1($password . $salt)));
    }

    public function createAdmin($username, $password, $role = '') {
        $model = new AdminModel();
        $admin = $model->getByUsername($username);

        if ($admin) {
            return false;
        }

        $salt = \PhalApi\Tool::createRandStr(64);

        $newAdmin = array(
            'username' => $username,
            'password' => $this->encryptPassword($password, $salt),
            'salt' => $salt,
            'role' => $role ? $role : self::ADMIN_ROLE_NORMAL,
            'state' => self::ADMIN_STATE_NORMAL,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $model->insert($newAdmin);

        return true;
    }
    
    public function getAdminRoles() {
        $model = new AdminRoleModel();
        return $model->getList();
    }
    
    public function getTotalNum() {
        $model = new AdminModel();
        return intval($model->getTotalNum());
    }
}
