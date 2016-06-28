<?php
//
//  Mobile.php
//  验证是否为手机号
//  Created by Summer on 02/07/16
//  Copyright (c) 2016 Summer. All rights reserved.
//  Contact email aer_c@qq.com or qq7579476
//  

class PhalApi_Request_Formatter_Mobile extends PhalApi_Request_Formatter_Base implements PhalApi_Request_Formatter {

    /**
     * 对数组格式化/数组转换
     * @param string $value 变量值
     * @return array
     */
    public function parse($value, $rule) {
        if (!preg_match("/1[34578]{1}\d{9}$/",$value)) {
            throw new PhalApi_Exception_BadRequest('手机号码格式错误');
        }

        return $value;
    }
}
