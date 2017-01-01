<?php
/**
 * 生成接口代码
 *
 * - 此脚本可用于生成基本的接口代码，包括Api, Domain, Model
 * - 非必须的，项目可根据自己喜好使用
 *
 * @author dogstar <chanzonghuang@gmail.com> 2016-05-14 
 */

define('CUR_PATH', dirname(__FILE__));

if ($argc < 3) {
    echo "\n";
    echo colorfulString("Usage:\n", 'WARNING');
    echo "   $argv[0] <app_path> <api_path> [author] [overwride]\n";
    echo "\n";

    echo colorfulString("Options:\n", 'WARNING');
    echo colorfulString('    app_path', 'NOTE'), "       Require. App relative path to PhalApi\n";
    echo colorfulString('    api_path', 'NOTE'), "       Require. Api relative path to app_pathe\n";
    echo colorfulString('    author', 'NOTE'), "         NOT require. Your great name here, default is empty\n";
    echo colorfulString('    overwride', 'NOTE'), "      NOT require. Whether overwrite, default is false\n";
    echo "\n";

    echo colorfulString("Demo:\n", 'WARNING');
    echo "    $argv[0] ./Demo ./User dogstar\n";
    echo "\n";

    echo colorfulString("Tips:\n", 'WARNING');
    echo "    This will create Api, Domain, and Model files if successfully.\n";
    echo "\n";

    exit(1);
}

// 接收参数
$appPath    = CUR_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . trim($argv[1]);
$apiPath    = str_replace('.php', '', ltrim(ltrim($argv[2], '.'), '/'));
$author     = isset($argv[3]) ? trim($argv[3]) : '';
$overwrite  = !empty($argv[4]) ? TRUE : FALSE;

$appPathRealPath = realpath($appPath);
if (!file_exists($appPathRealPath)) {
    echo colorfulString("$appPath not exists!\n", 'FAILURE');
    exit(1);
}

// 待生成的代码文件
$apiFilePath    = $appPath . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . $apiPath . '.php';
$domainFilePath = $appPath . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . $apiPath . '.php';
$modelFilePath  = $appPath . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . $apiPath . '.php';
// 检测是否重复生成
foreach (array($apiFilePath, $domainFilePath, $modelFilePath) as $file) {
    if (file_exists($file) && !$overwrite) {
        echo colorfulString("$file exists! Stop to create again!\n", 'FAILURE');
        exit(1);
    }
}

// 创建需要的目录
foreach (array($apiFilePath, $domainFilePath, $modelFilePath) as $file) {
    $toCreateFile = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR));
    if (!file_exists($toCreateFile)) {
        echo colorfulString("Start to create folder $toCreateFile ...\n");
        mkdir($toCreateFile, 0755, TRUE);
    }
}

// 准备模板变量
$API_NAME       = str_replace(DIRECTORY_SEPARATOR, '_', $apiPath);
$AUTHOR_NAME    = $author;
$CREATE_TIME    = date('Y-m-d H:i:s');
$TABLE_NAME     = strtolower($API_NAME);

// 生成代码
$helperFolder = CUR_PATH . DIRECTORY_SEPARATOR . 'PhalApi' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR;

foreach (array('Api', 'Domain', 'Model') as $type) {
    $apiCode = str_replace(
        array('{%API_NAME%}', '{%AUTHOR_NAME%}', '{%CREATE_TIME%}', '{%TABLE_NAME%}'), 
        array($API_NAME, $AUTHOR_NAME, $CREATE_TIME, $TABLE_NAME), 
        file_get_contents($helperFolder . "_{$type}.php.tpl")
    );

    $maps = array(
        'Api'       => $apiFilePath,
        'Domain'    => $domainFilePath,
        'Model'     => $modelFilePath,
    );

    $toSaveFilePath = $maps[$type];

    echo colorfulString("Start to create file $toSaveFilePath ...\n");
    file_put_contents($toSaveFilePath, $apiCode);
}

echo colorfulString("\nOK! ${apiPath} has been created successfully!\n\n", 'SUCCESS');

function colorfulString($text, $type = NULL) {
    $colors = array(
        'WARNING'   => '1;33',
        'NOTE'      => '1;36',
        'SUCCESS'   => '1;32',
        'FAILURE'   => '1;35',
    );

    if (empty($type) || !isset($colors[$type])){
        return $text;
    }

    return "\033[" . $colors[$type] . "m" . $text . "\033[0m";
}

function genSql($tableName, $tableKey, $sqlContent, $engine, $charset) {
    return sprintf("
CREATE TABLE `%s` (
    `%s` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    %s
    `ext_data` text COMMENT 'json data here',
     PRIMARY KEY (`%s`)
 ) ENGINE=%s DEFAULT CHARSET=%s;
            
", $tableName, $tableKey, $sqlContent, $tableKey, $engine, $charset);
}
