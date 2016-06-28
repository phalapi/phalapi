<?php

//
//  Md5.php
//  MD5简单验证
//  Created by Summer on 02/07/16
//  Copyright (c) 2016 Summer. All rights reserved.
//  Contact email aer_c@qq.com or qq7579476
//  

class PhalApi_Request_Formatter_Md5 extends PhalApi_Request_Formatter_Base implements PhalApi_Request_Formatter {

    /**
     * 对数组格式化/数组转换
     * @param string $value 变量值
     * @return array
     */
    public function parse($value, $rule) {
        if(!preg_match("/^[a-z0-9]{32}$/", $value)){
           throw new PhalApi_Exception_BadRequest('密码类型请使用MD5加密后再提交');
        }

        return $value;
    }
}
