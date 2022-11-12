<?php

namespace App\Api;

use PhalApi\Api;

/**
 * Hello wolrd
 */
class Hello extends Api {

	/**
	 * 示例接口
	 */
	public function world() {
		return array('content' => 'Hello World!');
	}
}