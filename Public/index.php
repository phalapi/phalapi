<?php
/**
 * 统一访问入口
 */

require_once dirname(__FILE__) . '/init.php';

$pai = new \PhalApi\PhalApi();
$pai->response()->output();

