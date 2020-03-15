<?php
/**
 * 创建一个新的应用插件
 * @author dogstar 20200311
 */

require_once dirname(__FILE__) . '/../public/init.php';

if ($argc < 2) {
    echo "Usage: {$argv[0]} <plugin_key>\n";
    echo "请输入你的插件编号，字母数字和下划线组合。\n";
    echo "例如：{$argv[0]} plugin_demo\n";
    echo "\n";
    exit;
}

$pluginKey = trim($argv[1]);
if (!preg_match('/^[0-9A-Za-z_]{1,}$/', $pluginKey)) {
    echo "插件编号格式不对，应该使用字母数字和下划线组合。\n";
    echo "\n";
    exit;
}

$pluginKeyClass = ucfirst(str_replace('_', '', $pluginKey));

// 生成插件json配置文件
echo "开始生成插件json配置文件……\n";
$jsonFile = dirname(__FILE__) . '/../plugins/' . $pluginKey . '.json';
$jsonConfig = array(
    'plugin_key' => $pluginKey,
    'plugin_name' => $pluginKey . '插件',
    'plugin_author' => '作者名称',
    'plugin_desc' => '插件描述',
    'plugin_version' => '1.0',
    'plugin_encrypt' => 0, // 加密模式，0无加密，1有加密，2半加密
    'plugin_depends' => array(
        'PHP' => '5.6',
        'MySQL' => '5.3',
        'PhalApi' => PHALAPI_VERSION, 
        // composer 依赖的包，无则不写
        'composer' => array(
            // 参考示例，格式是包名 => 版本号
            'phalapi/kernal' => '>=2.12.2',
        ),
        'extension' => array(
            // PHP扩展名
            // 'pdo_mysql',
        ),
    ),
    // 插件的文件
    'plugin_files' => array(
        // 配置文件
        'config' => 'config/' . $pluginKey . '.php',
        // 插件启动文件
        'plugins' => 'plugins/' . $pluginKey . '.php',
        // 数据库变更文件
        'data' => 'data/' . $pluginKey . '.sql',
        // portal后台及对外访问的文件
        'public' => array(
            'public/portal/page/' . $pluginKey,
            'public/portal/page/' . $pluginKey,
        ),
        // PHP源代码
        'src' => array(
            'src/app/Api/' . $pluginKeyClass,
            'src/app/Domain/' . $pluginKeyClass,
            'src/app/Model/' . $pluginKeyClass,
            'src/app/Common/' . $pluginKeyClass,
            'src/portal/Api/' . $pluginKeyClass,
        ),
    ),
);

if (file_exists($jsonFile)) {
    echo "插件已存在！" . $jsonFile . "\n";
    echo "\n";
    exit;
}
file_put_contents($jsonFile, json_encode($jsonConfig, version_compare(PHP_VERSION, '5.4.0', '>=') ? JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT : JSON_PRETTY_PRINT));

echo realpath($jsonFile), " json配置文件生成 ok \n\n";

// 开始创建插件文件和目录
echo "开始创建插件文件和目录……\n";

// config配置
$file = API_ROOT . '/' . $jsonConfig['plugin_files']['config'];
echo $file, "... \n";
file_put_contents($file, "<?php
// $pluginKey 插件配置
return array(
);
");

// json配置
$file = API_ROOT . '/' . $jsonConfig['plugin_files']['plugins'];
echo $file, "... \n";
file_put_contents($file, "<?php
// $pluginKey 插件初始化

");

// 添加菜单
$menuId = rand(100001, 999999999);
$menuSql = array();
$menuSql[] = "delete from `phalapi_portal_menu` where id = {$menuId};";
$menuSql[] = "insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '{$menuId}', '{$pluginKey}插件', 'page/{$pluginKey}/index.html', '9999', '1', 'fa fa-list-alt');";

$file = API_ROOT . '/' . $jsonConfig['plugin_files']['data'];
echo $file, "... \n";
file_put_contents($file, implode("\n", $menuSql) . "\n");

// 运营平台
$file = API_ROOT . '/public/portal/' . $pluginKey;
echo $file, "... \n";
mkdir(API_ROOT . '/public/portal/page/' . $pluginKey, 0755, TRUE);
$file = API_ROOT . '/public/portal/page/' . $pluginKey . '/index.html';
echo $file, "... \n";
file_put_contents($file, file_get_contents(API_ROOT . '/public/portal/page/phalapi-plugins/_index_tpl.html'));

// src源代码
mkdir(API_ROOT . '/src/app/Api/' . $pluginKeyClass, 0755, TRUE);
$file = API_ROOT . '/src/app/Api/' . $pluginKeyClass . '/Main.php';
echo $file, "... \n";
file_put_contents($file, "<?php
namespace App\\Api\\{$pluginKeyClass};
use PhalApi\\Api;

/**
 * {$pluginKey} 插件
 */
class Main extends Api {

    /**
     * 插件接口
     * @desc 待开发的插件新接口 
     */
    public function todo() {
    }
}

");

mkdir(API_ROOT . '/src/app/Domain/' . $pluginKeyClass, 0755, TRUE);
$file = API_ROOT . '/src/app/Domain/' . $pluginKeyClass . '/Main.php';
echo $file, "... \n";
file_put_contents($file, "<?php
namespace App\\Domain\\{$pluginKeyClass};

class Main {
}
");

mkdir(API_ROOT . '/src/app/Model/' . $pluginKeyClass, 0755, TRUE);
$file = API_ROOT . '/src/app/Model/' . $pluginKeyClass . '/Main.php';
echo $file, "... \n";
file_put_contents($file, "<?php
namespace App\\Model\\{$pluginKeyClass};
use PhalApi\\Model\\DataModel;

class Main extends DataModel {
}
");

mkdir(API_ROOT . '/src/app/Common/' . $pluginKeyClass, 0755, TRUE);

// portal
mkdir(API_ROOT . '/src/portal/Api/' . $pluginKeyClass, 0755, TRUE);

$file = API_ROOT . '/src/portal/Api/' . $pluginKeyClass . '/Main.php';
echo $file, "... \n";
file_put_contents($file, "<?php
namespace Portal\\Api\\{$pluginKeyClass};
use Portal\\Common\\DataApi as Api;

/**
 * {$pluginKey} 插件
 */
class Main extends Api {

    protected function getDataModel() {
        return new \\App\\Model\\{$pluginKeyClass}\\Main();
    }
}
");

echo "插件文件和目录生成 ok \n\n";

echo "开始添加运营平台菜单……\n";

foreach ($menuSql as $sql) {
    \PhalApi\DI()->notorm->demo->executeSql($sql);
}

echo "{$pluginKey}插件菜单添加 ok \n\n";

echo "恭喜，插件创建成功，可以开始开发啦！\n";


