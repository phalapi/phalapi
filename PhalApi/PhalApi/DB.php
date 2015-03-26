<?php
/**
 * PhalApi_DB数据库接口
 * 
 * @TODO 待接口统一
 * 
 * @package PhalApi\DB
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-09
 */
interface PhalApi_DB{

	public function connect();
	
	public function disconnect();
}
