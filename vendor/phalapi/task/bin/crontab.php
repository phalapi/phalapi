<?php
/**
 * 计划任务入口示例
 * ./bin/crontab.php 文件
 */

require_once dirname(__FILE__) . '/../public/init.php';

try {
    $progress = new PhalApi\Task\Progress();
    $progress->run();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}
