<?php
/**
 * 创建一个新的应用插件
 * @author dogstar 20200311
 */

require_once dirname(__FILE__) . '/../public/init.php';

if ($argc < 2) {
    echo "Usage: {$argv[0]} <plugin_key>\n";
    echo "请输入你的插件名称，字母数字和下划线组合。\n";
    echo "\n";
    exit;
}

$pluginKey = trim($argv[1]);
if (!preg_match('/^[0-9A-Za-z_]{1,}$/', $pluginKey)) {
    echo "插件标识格式不对，应该使用字母数字和下划线组合。\n";
    echo "\n";
    exit;
}

$pluginKeyClass = ucfirst(str_replace('_', $pluginKey));

// 生成插件json配置文件
echo "开始生成插件json配置文件……\n";
$jsonFile = dirname(__FILE__) . '/../plugins/' . $pluginKey . '.json';
$jsonConfig = array(
    'plugin_key' => $pluginKey,
    'plugin_name' => $pluginKey . '插件',
    'plugin_author' => '作者名称',
    'plugin_desc' => '插件描述',
    'plugin_version' => '1.0',
    'plugin_depends' => array(
        'PHP' => '5.6',
        'MySQL' => '5.3',
        'PhalApi' => PHALAPI_VERSION, 
        // composer 依赖的包，无则不写
        'composer' => array(
            // 参考示例，格式是包名 => 版本号
            // 'phalapi/kernal' => '2.12.0',
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
            'public/portal/' . $pluginKey,
            'public/portal/' . $pluginKey . '/index.html',
        ),
        // PHP源代码
        'src' => array(
            'src/Api/' . $pluginKeyClass . '.php',
            'src/Domain/' . $pluginKeyClass . '.php',
            'src/Model/' . $pluginKeyClass . '.php',
        ),
    ),
);

if (file_exists($jsonFile)) {
    echo "插件已存在！" . $jsonFile . "\n";
    echo "\n";
    exit;
}
file_put_contents($jsonFile, json_encode($jsonConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo realpath($jsonFile), " json配置文件生成 ok \n\n";

// 开始创建插件文件和目录
echo "开始创建插件文件和目录……\n";

file_put_contents("<?php
// $pluginKey 插件配置
return array(
);
", API_ROOT . '/' . $jsonConfig['plugin_files']['config']);

file_put_contents("<?php
// $pluginKey 插件初始化

", API_ROOT . '/' . $jsonConfig['plugin_files']['plugins']);

file_put_contents("
", API_ROOT . '/' . $jsonConfig['plugin_files']['data']);

mkdir(API_ROOT . '/public/portal/' . $pluginKey);
file_put_contents("$pluginKey Html模板", API_ROOT . '/public/portal/' . $pluginKey . '/index.html');

file_put_contents("<?php
namespace App\\Api;
use PhalApi\\Api;

/**
 * $pluginKey插件
 */
class {$pluginKeyClass} extends Api {

    /**
     * 插件接口
     * @desc 待开发的插件新接口 
     */
    public function todo() {
    }
}

", API_ROOT . '/src/Api/' . $pluginKeyClass . '.php');

file_put_contents("<?php
namespace App\Domain;

class {$pluginKeyClass} {
}
", API_ROOT . '/src/Domain/' . $pluginKeyClass . '.php');

file_put_contents("<?php
namespace App\Model;
use PhalApi\Model\DataModel;

class {$pluginKeyClass} extends DataModel {
}
", API_ROOT . '/src/Model/' . $pluginKeyClass . '.php');

echo "插件文件和目录生成 ok \n\n";

echo "恭喜，插件创建成功，可以开始开发啦！\n";


