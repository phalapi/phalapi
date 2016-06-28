<?php
//
//  Json.php
//  json解析
//  Created by Summer on 02/07/16
//  Copyright (c) 2016 Summer. All rights reserved.
//  Contact email aer_c@qq.com or qq7579476
//  

class PhalApi_Request_Formatter_Json extends PhalApi_Request_Formatter_Base implements PhalApi_Request_Formatter {
	public function parse($value, $rule) {

		$array = json_decode($value, TRUE);
		foreach ($rule['rule'] as $key => $val) {
    		DI()->request->getByRule($val, $array);
		}

		return $value;
	}
}