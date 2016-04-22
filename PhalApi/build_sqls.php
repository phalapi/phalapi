<?php
/**
 * 根据配置自动生成SQL建表语句
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-04 
 */

define('CUR_PATH', dirname(__FILE__));

if ($argc < 3) {
    echo "\n";
    echo colorfulString("Usage:\n", 'WARNING');
    echo "   $argv[0] <dbs_config> <table> [engine]\n";
    echo "\n";

    echo colorfulString("Options:\n", 'WARNING');
    echo colorfulString('    dbs_config', 'NOTE'), "        Require. Path to ./Config/dbs.php\n";
    echo colorfulString('    table', 'NOTE'), "             Require. Table name\n";
    echo colorfulString('    engine', 'NOTE'), "            NOT require. Database engine, default is Innodb\n";
    echo "\n";

    echo colorfulString("Demo:\n", 'WARNING');
    echo "    php ./build_sqls.php ../Config/dbs.php User\n";
    echo "\n";

    echo colorfulString("Tips:\n", 'WARNING');
    echo "    This will output the sql directly, enjoy yourself!\n";
    echo "\n";

    //echo "\n", implode("\n", array_keys($dbsConfig['tables'])), "\n\n";
    exit(1);
}

$dbsConfigFile = trim($argv[1]);
$tableName = trim($argv[2]);
$engine = isset($argv[3]) ? $argv[3] : 'InnoDB';

if (!file_exists($dbsConfigFile)) {
    echo colorfulString("Error: file $dbsConfigFile not exists!\n\n", 'FAILURE');
    exit();
}

$dbsConfig = include($dbsConfigFile);

if (empty($dbsConfig) || empty($dbsConfig['servers']) || empty($dbsConfig['tables'])
    || !is_array($dbsConfig['servers']) || !is_array($dbsConfig['tables'])) {
        echo colorfulString("Error: db config is incorrect, it should be format as: 

<?php

return array(
    /**
     * avaiable db servers
     */

    'servers' => array(
        'db_X' => array(
            'host'      => 'localhost',             //数据库域名
            'name'      => 'phalapi',               //数据库名字
            'user'      => 'root',                  //数据库用户名
            'password'  => '',	                    //数据库密码
            'port'      => '3306',                  //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),
    /**
     * custom table map
     */
    'tables' => array(
        'demo' => array(
            'prefix' => 'weili_',
            'key' => 'id',
            'map' => array(
                array('start' => 0, 'end' => 2, 'db' => 'db_X'),
            ),
        ),
    ),
);

", 'FAILURE');
    exit();
    }

$tableMap = isset($dbsConfig['tables'][$tableName]) ? $dbsConfig['tables'][$tableName] : $dbsConfig['tables']['__default__'];
if (empty($tableMap)) {
    echo colorfulString("Error: no table map for $tableName !\n\n", 'FAILURE');
    exit();
}

$tableMap['prefix'] = isset($tableMap['prefix']) ? trim($tableMap['prefix']) : '';
$tableMap['key'] = isset($tableMap['key']) ? trim($tableMap['key']) : 'id';
$tableMap['map'] = isset($tableMap['map']) ? $tableMap['map'] : array();

if (empty($tableMap['map'])) {
    echo colorfulString("Error: miss map for table $tableName !\n\n", 'FAILURE');
    exit();
}

$sqlFilePath = CUR_PATH . '/../Data/' . $tableName . '.sql';
if (!file_exists($sqlFilePath)) {
    echo colorfulString("Error: sql file $sqlFilePath not exists!\n\n", 'FAILURE');
    exit();
}

$sqlContent = file_get_contents($sqlFilePath);
$sqlContent = trim($sqlContent);

$outputSql = '';

foreach ($tableMap['map'] as $mapItem) {
    $dbName = isset($mapItem['db']) ? $mapItem['db'] : 'db';
    if (!isset($dbsConfig['servers'][$dbName])) {
        echo colorfulString("Error: no such db server as db = $dbName !\n\n", 'FAILURE');
        exit();
    }

    $outputSql .= "
/**
 * DB: {$dbsConfig['servers'][$dbName]['host']}  {$dbsConfig['servers'][$dbName]['name']}
 */
";

    $charset = isset($dbsConfig['servers'][$dbName]['charset']) 
        ? $dbsConfig['servers'][$dbName]['charset'] : 'utf8';

    if (isset($mapItem['start']) && isset($mapItem['end'])) {
        for ($i = $mapItem['start']; $i <= $mapItem['end']; $i ++) {
            $outputSql .= genSql(
                $tableMap['prefix'] . $tableName . '_' . $i, 
                $tableMap['key'], 
                $sqlContent, 
                $engine,
                $charset
            );
        }
    } else {
        $outputSql .= genSql($tableMap['prefix'] . $tableName, $tableMap['key'], $sqlContent, $engine, $charset);
    }
}


echo $outputSql;

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
