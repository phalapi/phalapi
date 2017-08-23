<?php
/**
 * 计划任务入口示例
 */

require_once dirname(__FILE__) . '/path/to/autoload.php';
require_once '/path/to/phalapi/config/di.php';

try {
    $progress = new PhalApi\Task\Progress();
    $progress->run();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}
