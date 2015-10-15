<?php

class PhalApiClientResponse {

    protected $ret = 200;
    protected $data = array();
    protected $msg = '';

    public function __construct($ret, $data = array(), $msg = '') {
        $this->ret = $ret;
        $this->data = $data;
        $this->msg = $msg;
    }

    public function getRet() {
        return $this->ret;
    }

    public function getData() {
        return $this->data;
    }

    public function getMsg() {
        return $this->msg;
    }
}
