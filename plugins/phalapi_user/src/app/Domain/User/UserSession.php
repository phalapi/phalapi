<?php
namespace App\Domain\User;
use App\Model\User\UserSession as UserSessionModel;

/**
 * 会话领域类
 */

class UserSession {

    const MAX_EXPIRE_TIME_FOR_SESSION = 2592000;    //一个月

    /**
     * 创建新的会话
     * @param int $userId 用户ID
     * @return string 会话token
     */
    public function generate($userId, $client = '')
    {
        if ($userId <= 0) {
            return '';
        }

        $token = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 64));

        $newSession = array();
        $newSession['user_id'] = $userId;
        $newSession['token'] = $token;
        $newSession['client'] = $client;
        $newSession['times'] = 1;
        $newSession['login_time'] = $_SERVER['REQUEST_TIME'];
        $newSession['expires_time'] = $_SERVER['REQUEST_TIME'] + self::getMaxExpireTime();

        $sessionModel = new UserSessionModel();
        $sessionModel->insert($newSession, $userId);

        return $token;
    }
    
    public function checkSession($user_id, $token) {
        $model = new UserSessionModel();
        $et = $model->getExpiresTime($user_id, $token);
        return $et > $_SERVER['REQUEST_TIME'] ? true : false;
    }

    public static function getMaxExpireTime() {
        return \PhalApi\DI()->config->get('phalapi_user.max_expire_time');
    }
}