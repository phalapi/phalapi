<?php
/**
 * 打包发布插件
 * @author dogstar 20200311
 */


require_once dirname(__FILE__) . '/../public/init.php';

if ($argc < 2) {
    echo "Usage: {$argv[0]} <plugin_key>\n";
    echo "请输入你的插件编号，字母数字和下划线组合。\n";
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

$jsonFile = dirname(__FILE__) . '/../plugins/' . $pluginKey . '.json';

if (!file_exists($jsonFile)) {
    echo "插件json配置文件不存在，", $jsonFile, " \n";
    echo "可以先使用 php ./bin/phalapi-plugin-create.php 脚本命令创建一个新的插件。\n";
    echo "\n";
    exit;
}

$config = json_decode(file_get_contents($jsonFile), true);

// 压缩多个文件
$fileList = $config['plugin_files'];
$fileList[] = 'plugins/' . $pluginKey . '.json';

$filename = API_ROOT . "/plugins/{$pluginKey}.zip"; // 压缩包所在的位置路径
@unlink($filename);

$zip = new ZipArchive();
$zip->open($filename,ZipArchive::CREATE);   //打开压缩包
foreach($fileList as $file){
    if (is_string($file)) {
        if (strpos($file, '.')) {
            $zip->addFile(API_ROOT . '/' . $file, $file);   //向压缩包中添加文件
        } else {
            addFileToZip(API_ROOT . '/' . $file, $file, $zip);
        }
    } else if (is_array($file)) {
        foreach ($file as $it) {
            if (strpos($it, '.')) {
                $zip->addFile(API_ROOT . '/' . $it, $it);   //向压缩包中添加文件
            } else {
                addFileToZip(API_ROOT . '/' . $it, $it, $zip);
            }
        }
    }
}
$zip->close();  //关闭压缩包

echo "插件已打包发布完毕！\n";
echo realpath($filename), "\n\n";


/**
 * @param $path 文件夹路径
 * @param $zip zip 对象
 */
function addFileToZip($path, $zipPath, $zip) {
    $handler = opendir($path); //打开当前文件夹由$path指定。

    // fixed: readdir() expects parameter 1 to be resource, boolean given
    if (!$handler) {
        return;
    }

    while (($filename = readdir($handler)) !== false) {
        if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归

                addFileToZip($path . "/" . $filename, $zipPath . '/' . $filename, $zip);
            } else { //将文件加入zip对象
                $zip->addFile($path . "/" . $filename, $zipPath . '/' . $filename);
            }
        }
    }
    @closedir($path);
}
