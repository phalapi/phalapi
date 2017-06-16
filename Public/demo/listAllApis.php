<?php
/**
 * PhalApi接口列表 - 自动生成
 * - 对Api_系列的接口，进行罗列
 * - 按service进行字典排序
 * - 支持多级目录扫描
 * <br>使用示例：<br>
 * ```
 * <?php
 * class Api_Demo extends PhalApi_Api {
 *      /**
 *       * 1.1 可在这里输入接口的服务名称
 *       * /
 *      public function index() {
 *          // todo ...
 *      }
 * }
 * ```
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      xiaoxunzhao 2015-10-25
 * @modify      Aevit, dogstar <chanzonghuang@gmail.com> 2014-10-29
 * @modify      shwy 2017-03-02
 */

require_once dirname(__FILE__) . '/../init.php';

/**
 * 项目的文件夹名
 * TODO: 请根据需要，修改成你的项目名称
 */
$apiDirName = 'Demo';

/**
 * 扩展类库
 * TODO: 请根据需要，添加需要显示的扩展路径，即./Api目录的父路径
 */
$libraryPaths = array(
    'Library/User/User',    // User扩展
    'Library/Auth/Auth',    // Auth扩展
    'Library/Qiniu/CDN',    // 七牛扩展
    'Library/WechatMini/WechatMini', // 微信小程序扩展
);

$apiList = new PhalApi_Helper_ApiList();
$apiList->render($apiDirName, $libraryPaths);

