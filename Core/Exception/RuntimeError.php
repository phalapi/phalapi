<?php
/**
 +------------------------------------------------------------------------------
 * Core_Exception_RuntimeError 服务器运行错误异常类
 +------------------------------------------------------------------------------
 * @author: dogstar 2014-10-02
 +------------------------------------------------------------------------------
 */

class Core_Exception_RuntimeError  extends Core_Exception
{
	public function __construct($message, $code = 0)
	{
		parent::__construct('Runtime Error: ' . $message, 2100 + $code);
	}
}
