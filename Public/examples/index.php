<?php
/**
 * Examples 统一入口
 */

require_once dirname(__FILE__) . '/../init.php';

//装载你的接口
DI()->loader->addDirs('Examples');

/** ---------------- 响应接口请求 ---------------- **/

$server = new PhalApi();
$rs = $server->response();
$rs->output();

