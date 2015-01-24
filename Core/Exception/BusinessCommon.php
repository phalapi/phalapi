<?php

/**
 * 通用业务异常类
 * @author yhh
 *
 */
class Core_Exception_BusinessCommon extends Core_Exception
{
	public function __construct($message, $code = 0){
		parent::__construct($message, $code);
	}
}