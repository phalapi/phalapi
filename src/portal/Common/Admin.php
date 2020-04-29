<?php
namespace Portal\Common;

use PhalApi\Exception\BadRequestException;

/**
 * 管理员会话服务
 * @author dogstar 20200307
 */
class Admin {

    // 管理员ID
    protected $id;

    // 管理员账号
    protected $username;

    // 管理员角色
    protected $role;

    const SESSION_KEY = 'phalapi_Portal';

    public function __construct() {
        // 检测session是否已启动
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        } else {
            // 如果 PHP < 5.4.0的版本，使用session_id()检查
            if(session_id() == '') {
                session_start();
            }
        }
    }

    /**
     * 管理员成功登录，开启会话
     */
    public function login($id, $username, $role) {
        $_SESSION[self::SESSION_KEY] = array(
            'id' => $id,
            'username' => $username,
            'role' => $role,
        );
    } 

    /**
     * 退出登录，清除会话
     */
    public function logout() {
        $_SESSION[self::SESSION_KEY] = array();
    }

    /**
     * 检测是否登录
     */
    public function check($isStopIfNoLogin = TRUE) {
        if (!empty($_SESSION[self::SESSION_KEY])) {
            return TRUE;
        }

        if ($isStopIfNoLogin) {
            throw new BadRequestException('管理员未登录', 6);
        }

        return FALSE; 
    }
    
    /**
     * 是否为超级管理员
     * @return boolean
     */
    public function isSuperAdmin() {
        return $this->role == 'super';
    }
    
    /**
     * 是否为游客
     * @return boolean
     */
    public function isGuest() {
        return $this->check(FALSE);
    }

    public function __get($name) {
        return isset($_SESSION[self::SESSION_KEY][$name]) ? $_SESSION[self::SESSION_KEY][$name] : NULL;
    }
}
