<?php
/**
 * 通过curl实现的快捷方便的接口请求类
 *
 * @author dogstar 2015-01-02
 */

class PhalApi_CUrl {

    public function get($url, $timeoutMs = 3000) {
        return $this->request($url, false, $timeoutMs);
    } 

    public function post($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs);
    }

    protected function request($url, $data, $timeoutMs = 3000) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }
}
