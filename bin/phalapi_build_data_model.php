<?php
/**
 * 根据数据库表，生成DataModel代码
 * @author dogstar 2020-06-10
 */

if ($argc < 2) {
    echo "\n";
    echo colorfulString("Usage:\n", 'WARNING');
    echo "   $argv[0] <dbs_config> [table] [project=app]\n";
    echo "\n";

    echo colorfulString("Options:\n", 'WARNING');
    echo colorfulString('    dbs_config', 'NOTE'), "        Require. Database config file name, such as dbs.php\n";
    echo colorfulString('    table', 'NOTE'), "             NOT Require. Table name, default is ALL tables\n";
    echo colorfulString('    project', 'NOTE'), "           NOT require. Project name to save PHP code, default is app\n";
    echo "\n";

    echo colorfulString("Demo:\n", 'WARNING');
    echo "    $argv[0] dbs.php \n";
    echo "\n";

    exit(1);
}

require_once dirname(__FILE__) . '/../public/init.php';

$configFile = dirname(__FILE__) . '/../config/' . $argv[1];
if (!file_exists($configFile)) {
    echo colorfulString($configFile . ' NOT exists!', 'FAILURE');
    exit(1);
}
$table = !empty($argv[2]) ? trim($argv[2]) : '';
$project = !empty($argv[3]) ? trim($argv[3]) : 'app';

$modelFolder =dirname(__FILE__) . '/../src/'.$project.'/Model';
@mkdir($modelFolder, 755, true);

$dbConfig = include($configFile);
$prefix = $dbConfig['tables']['__default__']['prefix'];
$di->notorm_tmp = new PhalApi\Database\NotORMDatabase($dbConfig, true);

$dbRes = $di->notorm_tmp->demo->queryAll('SHOW TABLES;');

foreach ($dbRes as $it) {
    $it = array_values($it);
    $curTable = $it[0];
    if (!empty($table) && $table != $curTable) {
        // 指定表，不匹配
        continue;
    }
    
    echo colorfulString("开始处理表：{$curTable} ...\n");
    
    $curTableWithoutPrefix = $curTable;
    if (substr($curTableWithoutPrefix, 0, strlen($prefix)) == $prefix) {
        $curTableWithoutPrefix = substr($curTableWithoutPrefix, strlen($prefix));
    }
    
    $className = tableName2ClassName($curTableWithoutPrefix);
    $classFilePath = $modelFolder . '/' . $className . '.php';
    if (file_exists($classFilePath)) {
        echo colorfulString("类文件已存在：{$classFilePath} ...\n", 'WARNING');
        continue;
    }
    
    $code = createDataModelPHPCode($project, $className, $curTableWithoutPrefix);
    file_put_contents($classFilePath, $code);
    echo colorfulString("Model代码已生成到：{$classFilePath} ...\n", 'SUCCESS');
}

$basePath = $modelFolder . '/Base.php';
if (file_exists($basePath)) {
    echo colorfulString("类文件已存在：{$basePath} ...\n", 'WARNING');
} else {
    file_put_contents($modelFolder . '/Base.php', createBaseClassCode($project));
    echo colorfulString("Model基类代码已生成到：{$basePath} ...\n", 'SUCCESS');
}

function createBaseClassCode($project) {
    $project = ucfirst($project);
    return <<<EOT
<?php
namespace {$project}\Model;

/**
 * 连接其他数据库
 * - 当需要连接和操作其他数据库时，请在Model继续此基类，以便切换数据库
 * - 或在此基类进行通用操作的封装
 */
class Base extends \PhalApi\Model\DataModel {

    /**
     * 切换数据库
     * @return \PhalApi\Database\NotORMDatabase
     */
    protected function getNotORM() {
        return \PhalApi\DI()->notorm;
    }
}

EOT;
}
function createDataModelPHPCode($project, $className, $curTableWithoutPrefix) {
    $project = ucfirst($project);
    return <<<EOT
<?php
namespace {$project}\Model;

class {$className} extends Base {

    public function getTableName(\$id) {
        return '{$curTableWithoutPrefix}';
    }
}

EOT;

}

function tableName2ClassName($curTableWithoutPrefix) {
    $arr = explode('_', $curTableWithoutPrefix);
    $str = '';
    foreach ($arr as $it) {
        $str .= ucfirst($it);
    }
    return $str;
}

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

