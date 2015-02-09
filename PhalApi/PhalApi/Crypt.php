<?php
/**
 * 对称加密
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

interface PhalApi_Crypt {

    public function encrypt($data, $key);
    
    public function decrypt($data, $key);
}
