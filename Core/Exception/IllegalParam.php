<?php
/**
 +------------------------------------------------------------------------------
 * Core_Exception_IllegalParam 非法参数异常类
 +------------------------------------------------------------------------------
 * @author: dogstar 2014-10-02
 +------------------------------------------------------------------------------
 */

class Core_Exception_IllegalParam  extends Core_Exception
{
	public function __construct($message, $code = 0)
	{
		parent::__construct('Illegal Param: ' . $message, 1200 + $code);
	}
}
