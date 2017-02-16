<?php
/**
 * Demo entrance
 */

require_once dirname(__FILE__) . '/../init.php';

// load your API folder
DI()->loader->addDirs('Demo');

/** ---------------- deal with API request ---------------- **/

$api = new PhalApi();
$rs = $api->response();
$rs->output();

