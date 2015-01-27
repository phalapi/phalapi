<?php

require_once dirname(__FILE__) . '/../init.php';

//数据操作 - 基于NotORM
Core_DI::one()->notorm = function() {
    return new Core_DB_NotORM(Core_DI::one()->config->get('dbs_for_examples'), false);
};

/** ---------------- 响应接口请求 ---------------- **/

$server = new PhalApi();

$rs = $server->response();

$rs->output();

