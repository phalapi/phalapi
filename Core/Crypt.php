<?php
/**
 * 对称加密
 *
 * @author: dogstar 2014-12-10
 */

interface Core_Crypt
{
    public function encrypt($data, $key);
    
    public function decrypt($data, $key);
}
