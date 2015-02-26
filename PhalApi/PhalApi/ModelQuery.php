<?php
/**
 * 查询对象(值对象)
 *
 * - 我们强烈建议应将此继承类的实例当作值对象处理，虽然我们提供了便利的结构化获取
 * - 如需要拷贝值对象，可以结合使用构造函数和toArray()
 * 
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

class PhalApi_ModelQuery {

	public $readCache = true;

	public $writeCache = true;

	public $id;
	
	public $timestamp;

	public function __construct($queryArr = array()) {
		$this->timestamp = $_SERVER['REQUEST_TIME'];

		foreach ($queryArr as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __set($name, $value) {
		$this->$name = $value;
	}

	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		}

		return NULL;
	}

	public function toArray() {
        return get_object_vars($this);
	}
}
