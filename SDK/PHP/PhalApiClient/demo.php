<?php

require_once dirname(__FILE__) . '/PhalApiClient.php';

$rs = PhalApiClient::create()
    ->withHost('http://demo.phalapi.net/')
    ->withService('Default.Index')
    ->withParams('username', 'dogstar')
    ->withTimeout(3000)
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());

/**

int(200)

array(4) {
    ["title"]=>
    string(12) "Hello World!"
    ["content"]=>
    string(36) "dogstar您好，欢迎使用PhalApi！"
    ["version"]=>
    string(5) "1.2.1"
    ["time"]=>
    int(1444925238)
}

string(0) ""

*/
