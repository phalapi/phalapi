#!/usr/bin/env php
<?php
require_once dirname(__FILE__) . '/../../../../Public/init.php';
//require_once '/home/dogstar/projects/library.phalapi.net/Public/init.php';

DI()->loader->addDirs('Demo');

if ($argc < 2) {
    echo "Usage: $argv[0] <service> \n\n";
    exit(1);
}

$service = trim($argv[1]);

echo "Input params(json):\n";
$params = trim(fgets(STDIN));

$params = json_decode($params, true);
if (is_array($params)) {
    $params = array();
}

$mq = new Task_MQ_Array();
$taskLite = new Task_Lite($mq);

$taskLite->add($service, $params);

$runner = new Task_Runner_Local($mq);
$rs = $runnter->go($service);

echo "\nDone:\n", json_encode($rs), "\n\n";

