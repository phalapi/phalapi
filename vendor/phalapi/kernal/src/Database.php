<?php
namespace PhalApi;

/**
 * PhalApi\Database 数据库接口
 * 
 * @TODO 待接口统一
 * 
 * @package     PhalApi\DB
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

interface Database {

	public function connect();
	
	public function disconnect();
}
