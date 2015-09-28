<?php
require_once dirname(__FILE__) . '/../../Public/init.php';
//require_once '/home/dogstar/projects/library.phalapi.net/Public/init.php';

DI()->loader->addDirs(array('Demo', 'Library', 'Library/Task/Task'));

try {
    $progress = new Task_Progress();
    $progress->run();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}
