<?php
/**
 * PhalApi_Cache 缓存接口
 *
 * @author dogstar 2015-02-04
 */

interface PhalApi_Cache {

    public function set($key, $value, $expire = 600);

    public function get($key);

    public function delete($key);
}
