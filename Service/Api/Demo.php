<?php
/**
 * 示例接口服务
 *
 * @author: dogstar 2014-10-29
 */

class Api_Demo extends Core_Api
{
    public function getRules()
    {
        return array(
            'test' => array(
                'username' => array(
                    'name' => 'username', 'type' => 'string', 'require' => true, 'default' => 'nobody',
                ),
            ),
        );
    }

    public function test()
    {
        return array('content' => 'Hello ' . $this->username, 'time' => $_SERVER['REQUEST_TIME']);
    }
}

