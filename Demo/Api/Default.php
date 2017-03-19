<?php
/**
 * Default API class
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Default extends PhalApi_Api {

    public function getRules() {
        return array(
            'index' => array(
                'username' 	=> array('name' => 'username', 'default' => 'PHPer', ),
            ),
        );
    }

    /**
     * Default API service
     * @return string title title
     * @return string content content
     * @return string version versio with format: X.X.X
     * @return int time current timestamp
     */
    public function index() {
        return array(
            'title' => 'Hello World!',
            'content' => T('Hi {name}, welcome to use PhalApi!', array('name' => $this->username)),
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        );
    }
}
