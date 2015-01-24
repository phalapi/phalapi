<?php
/**
 +------------------------------------------------------------------------------
 * Core_Exception_RuleError 参数错误
 +------------------------------------------------------------------------------
 * @author: dogstar 2014-10-12
 +------------------------------------------------------------------------------
 */

class Core_Exception_RuleError extends Core_Exception
{
	public function __construct($message, $code = 0)
	{
		parent::__construct('Param Rule Error: ' . $message, 2200 + $code);
	}
}
