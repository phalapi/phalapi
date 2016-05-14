<?php
/**
 * Api_{%API_NAME%}
 * @author {%AUTHOR_NAME%} {%CREATE_TIME%}
 */

class Api_{%API_NAME%} extends PhalApi_Api {

    public function getRules() {
        return array(
            'go' => array(
            ),
        );
    }

    /**
     * go接口
     * @desc go接口描述
     * @return int code 状态码，0表示成功，非0表示失败
     * @return string msg 状态提示
     */
    public function go() {
        $rs = array('code' => 0, 'msg' => '');

        // TODO
        $domain = new Domain_{%API_NAME%}();
        $domain->go();

        return $rs;
    }
}
