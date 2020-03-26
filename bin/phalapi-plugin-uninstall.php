<?php
/**
 * 卸载插件
 * @author dogstar 20200326
 */


require_once dirname(__FILE__) . '/../public/init.php';

if ($argc < 2) {
    echo "Usage: {$argv[0]} <plugin_key>\n";
    echo "请输入待卸载的插件编号。\n";
    echo "\n";
    exit;
}

$pluginKey = trim($argv[1]);
if (!preg_match('/^[0-9A-Za-z_]{1,}$/', $pluginKey)) {
    echo "插件编号格式不对，应该使用字母数字和下划线组合。\n";
    echo "\n";
    exit;
}

$plugin = new Portal\Domain\Plugin();
$detail = [];

$plugin->uninstall($pluginKey, $detail);

echo implode("\n", $detail);

echo "\n";

