<?php

/** SQL literal value
*/
class NotORM_Literal {
	protected $value = '';
	
	/** @var array */
	public $parameters = array();
	
	/** Create literal value
	* @param string
	* @param mixed parameter
	* @param mixed ...
	*/
	function __construct($value) {
		$this->value = $value;
		$this->parameters = func_get_args();
		array_shift($this->parameters);
	}
	
	/** Get literal value
	* @return string
	*/
	function __toString() {
		return $this->value;
	}
	
}
