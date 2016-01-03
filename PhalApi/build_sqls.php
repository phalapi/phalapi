<?php
/**
 * 根据配置自动生成SQL建表语句
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-04 
 */

define('CUR_PATH', dirname(__FILE__));

if ($argc < 3) {
    echo "Usage: $argv[0] <dbs.php> <table> [engine=InnoDB]\n\n";
    //echo "\n", implode("\n", array_keys($dbsConfig['tables'])), "\n\n";
    exit(1);
}

$dbsConfigFile = trim($argv[1]);
$tableName = trim($argv[2]);
$engine = isset($argv[3]) ? $argv[3] : 'InnoDB';

if (!file_exists($dbsConfigFile)) {
    echo "Error: file $dbsConfigFile not exists!\n";
    exit();
}

$dbsConfig = include($dbsConfigFile);

if (empty($dbsConfig) || empty($dbsConfig['servers']) || empty($dbsConfig['tables'])
    || !is_array($dbsConfig['servers']) || !is_array($dbsConfig['tables'])) {
        echo "Error: db config is incorrect, it should be format as: 

<?php
        return array(
            /**
             * avaiable db servers
             */

            'servers' => array(
                'db_X' => array(
                    'host'      => 'localhost',         //数据库域名
                    'name'      => 'demo',              //数据库名字
                    'user'      => 'root',              //数据库用户名
                    'password'  => '123456',            //数据库密码
                    'port'      => '3306',              //数据库端口
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

";
    exit();
    }

$tableMap = isset($dbsConfig['tables'][$tableName]) ? $dbsConfig['tables'][$tableName] : $dbsConfig['tables']['__default__'];
if (empty($tableMap)) {
    echo "Error: no table map for $tableName !\n";
    exit();
}

$tableMap['prefix'] = isset($tableMap['prefix']) ? trim($tableMap['prefix']) : '';
$tableMap['key'] = isset($tableMap['key']) ? trim($tableMap['key']) : 'id';
$tableMap['map'] = isset($tableMap['map']) ? $tableMap['map'] : array();

if (empty($tableMap['map'])) {
    echo "Error: miss map for table $tableName !\n";
    exit();
}

$sqlFilePath = CUR_PATH . '/../Data/' . $tableName . '.sql';
if (!file_exists($sqlFilePath)) {
    echo "Error: sql file $sqlFilePath not exists!\n";
    exit();
}

$sqlContent = file_get_contents($sqlFilePath);
$sqlContent = trim($sqlContent);

$outputSql = '';

foreach ($tableMap['map'] as $mapItem) {
    $dbName = isset($mapItem['db']) ? $mapItem['db'] : 'db';
    if (!isset($dbsConfig['servers'][$dbName])) {
        echo "Error: no such db server as db = $dbName !\n";
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
