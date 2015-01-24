<?php
/**
 * 统一入口
 */

require_once dirname(__FILE__) . '/init.php';

/** ---------------- 响应接口请求 ---------------- **/

$server = new PhalApi();

$rs = $server->response();

$rs->output();
