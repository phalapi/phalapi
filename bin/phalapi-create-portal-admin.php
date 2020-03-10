<?php
require_once dirname(__FILE__) . '/../public/init.php';

if ($argc < 3) {
    echo "Usage: {$argv[0]} <username> <password> [role=admin|super]\n";
    echo "\n";
    exit;
}

$username = $argv[1];
$password = $argv[2];
$role = isset($argv[3]) ? $argv[3] : '';

$domain = new Portal\Domain\Admin();
if ($domain->createAdmin($username, $password, $role)) {
    echo "运营平台管理员账号创建成功！\n";
} else {
    echo "运营平台管理员账号已存在，不能重复创建！\n";
}

