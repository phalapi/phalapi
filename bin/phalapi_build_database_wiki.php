<?php
/**
 * 生成数据库字典文档
 * 
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author: dogstar <chanzonghuang@gmail.com> 2025-03-31
 */

require_once dirname(__FILE__) . '/../public/init.php';
$databaseName = \PhalApi\DI()->config->get('dbs.servers.db_master.name');
echo "开始生成数据库字典文件 {$databaseName} ... ", PHP_EOL;

$tabeRows = \PhalApi\DI()->notorm->demo->queryAll('SHOW TABLES;', array());

$mdContent = "# {$databaseName}数据库字典\n\n";

foreach ($tabeRows as $it) {
    $it = array_values($it);
    $tableName = $it[0];

    $sqlTD = "SELECT
        table_comment 
        FROM
        information_schema.`TABLES`
        WHERE
        table_schema = '{$databaseName}'
        and table_name = '{$tableName}'";
    $fieldsRowsTD = \PhalApi\DI()->notorm->demo->queryAll($sqlTD, array());
    $tableDesc = isset($fieldsRowsTD[0]['table_comment']) ? $fieldsRowsTD[0]['table_comment'] : '';

    $mdContent .= "## {$tableName}表结构 {$tableDesc}\n";

    $mdContent .= "字段|类型|默认值|是否允许为NULL|索引|注释  \n";
    $mdContent .= "---|---|---|---|---|---  \n";

    $sql = "show full fields from {$tableName}";
    $fieldsRows = \PhalApi\DI()->notorm->demo->queryAll($sql, array());
    foreach ($fieldsRows as $it) {
        $mdContent .= sprintf("%s|%s|%s|%s|%s|%s  \n", str_replace(['\r\n',"\r\n","\n"],['',"",""],$it['Field']), $it['Type'], $it['Default'], $it['Null'] == 'YES' ? '允许NULL' : '不为NULL', getKeyStr($it['Key']), str_replace(['\r\n',"\r\n","\n"],['',"",""],$it['Comment']));
    }
    $mdContent .= "\n\n";
}

$mdFile = API_ROOT . '/data/phalapi_database_wiki.md';
file_put_contents($mdFile, $mdContent);

echo "$mdFile 已保存！", PHP_EOL;

function getKeyStr($key) {
    $map = array(
        'UNI' => '',
        'MUL' => '',
    );
    return $key;
}
