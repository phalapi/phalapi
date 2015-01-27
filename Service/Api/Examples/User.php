<?php

class Api_Examples_User extends Core_Api
{
    public function getRules()
    {
        return array(
            'getBaseInfo' => array(
                'userId' => array('name' => 'userId', 'type' => 'int', 'min' => 1, 'require' => true),
            ),
        );
    }

    public function getBaseInfo()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Examples_User();
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
}
