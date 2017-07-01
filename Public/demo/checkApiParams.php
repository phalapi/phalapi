<?php
/**
 * 工具 - 查看接口参数规则
 */

require_once dirname(__FILE__) . '/../init.php';

$projectName = 'PhalApi开源接口框架';

/**
 * TODO: 装载你的接口
 */
DI()->loader->addDirs('Demo');

/**
 * 扩展类库
 *
 * TODO: 请根据需要，添加需要显示的扩展路径，即./Api目录的父路径
 */
$libraryPaths = array(
    'Library/User/User',    // User扩展
    'Library/Auth/Auth',    // Auth扩展
    'Library/Qiniu/CDN',    // 七牛扩展
    'Library/WechatMini/WechatMini', // 微信小程序扩展
);

foreach ($libraryPaths as $aPath) {
    $toAddDir = str_replace('/', DIRECTORY_SEPARATOR, $aPath);
    DI()->loader->addDirs($toAddDir);
}

$apiDesc = new PhalApi_Helper_ApiDesc($projectName);
$apiDesc->render();

