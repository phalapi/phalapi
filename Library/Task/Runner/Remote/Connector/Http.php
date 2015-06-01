<?php

class Task_Runner_Remote_Connector_Http extends Task_Runner_Remote_Connector {

    protected function doRequest($url, $data, $timeoutMs) {
        $curl = DI()->get('curl', 'PhalApi_CUrl');

        return $curl->post($url, $data, $timeoutMs);
    }
}
