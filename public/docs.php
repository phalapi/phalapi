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

use PhalApi\Helper\ApiStaticCreate;
use PhalApi\Helper\ApiList;
use PhalApi\Helper\ApiDesc;

require_once dirname(__FILE__) . '/init.php';

if (!empty($_GET['language'])) {
    \PhalApi\SL($_GET['language']);
    setcookie('language', $_GET['language'], time() + 86400 * 360, '/');
}

$projectName = \PhalApi\T('PhalApi API Framework');
$docViewCode = ''; // 查看文档密码，为空时不限制

$detailTpl = API_ROOT . '/src/view/docs/api_desc_tpl.php';
$listTpl = API_ROOT . '/src/view/docs/api_list_tpl.php';

if (substr(PHP_SAPI, 0, 3) == 'cli') {
    // 生成离线文档
    $apiHtml = new ApiStaticCreate($projectName, 'fold', $detailTpl);
    $apiHtml->render($listTpl);
} else if (!empty($_GET['detail'])) {
    checkViewCode();

    // 接口详情页
    $apiDesc = new ApiDesc($projectName);
    $apiDesc->render($detailTpl);
} else {
    checkViewCode();

    // 接口列表页
    $apiList = new ApiList(
        $projectName,                               // 项目名称
        ApiList::API_CATE_TYPE_API_CLASS_TITLE,     // 菜单分组：按接口自定义名称
        ApiList::API_LIST_SORT_BY_API_TITLE         // 接口排序：按接口自定义标题
    );
    $apiList->render($listTpl);
}

/**
 * 检测查看密码
 */
function checkViewCode() {
    // 不设置查看密码，则不限制
    global $projectName, $docViewCode;
    if (empty($docViewCode)) {
        return;
    }
    $docViewCode = strval($docViewCode);

    session_start();

    $submitError = NULL;
    if (!empty($_POST['view_code'])) {
        if ($_POST['view_code'] == $docViewCode) {
            $_SESSION['doc_view_code'] = $docViewCode;
        } else {
            $submitError = \PhalApi\T('wrong view password');
        }
    }

    if (empty($_SESSION['doc_view_code']) || $_SESSION['doc_view_code'] != $docViewCode) {
        include API_ROOT . '/src/view/docs/check_view_code.php';
        die();
    }
}
