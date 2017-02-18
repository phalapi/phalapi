<?php

class Api_User extends PhalApi_Api {

    public function getRules() {
        return array(
            'getBaseInfo' => array(
                'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => 'user ID'),
            ),
            'getMultiBaseInfo' => array(
                'userIds' => array('name' => 'user_ids', 'type' => 'array', 'format' => 'explode', 'require' => true, 'desc' => 'user ID, explore with comma '),
            ),
        );
    }

    /**
     * Get user base info
     * @desc You can get single user base info.
     * @return int code operation code, 0: ok, 1: user not exists
     * @return object info user information obejct
     * @return int info.id user ID
     * @return string info.name username
     * @return string info.note user note
     * @return string msg tips
     */
    public function getBaseInfo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_User();
        $info = $domain->getBaseInfo($this->userId);

        if (empty($info)) {
            DI()->logger->debug('user not found', $this->userId);

            $rs['code'] = 1;
            $rs['msg'] = T('user not exists');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }

    /**
     * Multi get users base info
     * @desc You can get servaral users base info in one time.
     * @return int code operation code, 0: ok
     * @return array list user info list
     * @return int list[].id user ID
     * @return string list[].name username
     * @return string list[].note user note
     * @return string msg  tips
     * @exception 400 params error!
     * @exception 500 inner error!
     */
    public function getMultiBaseInfo() {
        $rs = array('code' => 0, 'msg' => '', 'list' => array());

        $domain = new Domain_User();
        foreach ($this->userIds as $userId) {
            $rs['list'][] = $domain->getBaseInfo($userId);
        }

        return $rs;
    }
}
