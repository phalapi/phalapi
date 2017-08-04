<?php

require_once dirname(__FILE__) . '/PhalApiClient.php';

$client = PhalApiClient::create()
        ->withHost('http://demo.phalapi.net/');

$rs = $client->reset()
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

echo "\n--------------------\n";

//one more time
$rs = $client->reset()
    ->withService("User.GetBaseInfo")
    ->withParams("user_id", "1")
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());


echo "\n--------------------\n";

//illegal request
$rs = $client->reset()
    ->withService("XXX.XXXXX")
    ->withParams("user_id", "1")
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());

