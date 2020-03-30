<?php
require_once dirname(__FILE__) . '/PhalApiClient.php';

$client = PhalApiClient::create()
    ->withHost('{url}');