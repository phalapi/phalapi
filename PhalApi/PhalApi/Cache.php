<?php
/**
 * PhalApi_Cache 缓存接口
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

interface PhalApi_Cache {

    public function set($key, $value, $expire = 600);

    /**
    * @return mixed/NULL 失败情况下返回NULL
    */
    public function get($key);

    public function delete($key);
}
