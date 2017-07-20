<?php
/**
 * PhalApi在线接口列表文档 - 自动生成
 *
 * - 对Api_系列的接口，进行罗列
 * - 按service进行字典排序
 * - 支持多级目录扫描
 *
 * <br>使用示例：<br>
 * ```
 * <?php
 * // 左侧菜单说明
 * class Demo extends Api {
 *      /**
 *       * 接口服务名称
 *       * @desc 更多说明
 *       * /
 *      public function index() {
 *      }
 * }
 * ```
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      xiaoxunzhao     2015-10-25
 * @modify      Aevit           2014-10-29
 * @modify      shwy            2017-03-02
 * @modify      dogstar         2017-06-17
 */

require_once dirname(__FILE__) . '/init.php';

$projectName = 'PhalApi开源接口框架';

/**
 * 扩展类库
 * TODO: 请根据需要，添加需要显示的扩展路径，即./Api目录的父路径
 */
//$libraryPaths = array(
//    'Library/User/User',    // User扩展
//    'Library/Auth/Auth',    // Auth扩展
//    'Library/Qiniu/CDN',    // 七牛扩展
//    'Library/WechatMini/WechatMini', // 微信小程序扩展
//);

if (!empty($_GET['detail'])) {
    $apiDesc = new \PhalApi\Helper\ApiDesc($projectName);
    $apiDesc->render();
} else {
    $apiList = new \PhalApi\Helper\ApiList($projectName);
    $apiList->render();
}

