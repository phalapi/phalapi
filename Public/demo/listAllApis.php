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
 * @modify      shwy, Aevit, dogstar <1179758693@qq.com> 2017-03-02
 */

define("D_S", DIRECTORY_SEPARATOR);
$root = dirname(__FILE__);
$env = (PHP_SAPI == 'cli') ? TRUE : FALSE;

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

// 主题风格，fold = 折叠，expand = 展开
$theme = isset($_GET['type']) ? $_GET['type'] : 'fold';
if ($env) {
    $theme = isset($argv[1]) ? $argv[1] : 'fold';
}
if (!in_array($theme, array('fold', 'expand'))) {
    $theme = 'fold';
}

// 初始化
require_once implode(D_S, array($root, '..', 'init.php'));

// 处理项目
DI()->loader->addDirs($apiDirName);
$files = listDir(implode(D_S, array($root, '..', '..', $apiDirName, 'Api')));

// 追加处理扩展类库
foreach ($libraryPaths as $aPath) {
    $toAddDir = str_replace('/', D_S, $aPath);
    DI()->loader->addDirs($toAddDir);

    $toListDir = API_ROOT . D_S . $toAddDir . D_S . 'Api';
    $aLibFiles = listDir($toListDir);

    $files = array_merge($files, $aLibFiles);
}

// 待排除的方法
$allPhalApiApiMethods = get_class_methods('PhalApi_Api');

$allApiS = array();
$errorMessage = '';

foreach ($files as $value) {
    $value    = realpath($value);
    $subValue = substr($value, strpos($value, D_S . 'Api' . D_S) + 1);
    //支持多层嵌套，不限级
    $arr                = explode(D_S, $subValue);
    $subValue           = implode(D_S, $arr);
    $apiServer          = str_replace(array(D_S, '.php'), array('_', ''), $subValue);
    $apiServerShortName = substr($apiServer, 4);

    if (!class_exists($apiServer)) {
        continue;
    }

    // 检测文件路径的合法性
    if (ucfirst(substr($apiServer, 4)) != substr($apiServer, 4)) {
        $errorMessage .= $apiServer . ' 类文件首字母必须大写！<br/>';
    }

    //  左菜单的标题
    $ref        = new ReflectionClass($apiServer);
    $title      = "//请检测接口服务注释($apiServer)";
    $desc       = '//请使用@desc 注释';
    $docComment = $ref->getDocComment();
    if ($docComment !== false) {
        $docCommentArr = explode("\n", $docComment);
        $comment       = trim($docCommentArr[1]);
        $title         = trim(substr($comment, strpos($comment, '*') + 1));
        foreach ($docCommentArr as $comment) {
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $desc = substr($comment, $pos + 5);
            }
        }
    }
    $allApiS[$apiServerShortName]['title'] = $title;
    $allApiS[$apiServerShortName]['desc']  = $desc;

    $method = array_diff(get_class_methods($apiServer), $allPhalApiApiMethods);
    sort($method);
    foreach ($method as $mValue) {
        $rMethod = new Reflectionmethod($apiServer, $mValue);
        if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
            continue;
        }

        $title      = '//请检测函数注释';
        $desc       = '//请使用@desc 注释';
        $docComment = $rMethod->getDocComment();
        if ($docComment !== false) {
            $docCommentArr = explode("\n", $docComment);
            $comment       = trim($docCommentArr[1]);
            $title         = trim(substr($comment, strpos($comment, '*') + 1));

            foreach ($docCommentArr as $comment) {
                $pos = stripos($comment, '@desc');
                if ($pos !== false) {
                    $desc = substr($comment, $pos + 5);
                }
            }
        }
        $service                                           = $apiServerShortName . '.' . ucfirst($mValue);
        $allApiS[$apiServerShortName]['methods'][$service] = array(
            'service' => $service,
            'title'   => $title,
            'desc'    => $desc,
        );
    }

}
//echo json_encode($allApiS) ;
//字典排列
ksort($allApiS);

function listDir($dir) {
    $dir .= substr($dir, -1) == D_S ? '' : D_S;
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}

function saveHtml($name, $string){
    $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc';
    if (!is_dir ( $dir)){
        mkdir ( $dir);
    }
    $handle = fopen ( $dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite ( $handle, $string);
    fclose ( $handle);
}

$env && ob_start ();
$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $apiDirName; ?> - 接口列表</title>
    <link href="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>
<br/>


<div class="ui text container" style="max-width: none !important; width: 1200px" id="menu_top">
    <div class="ui floating message">
        <?php
        if (!empty($errorMessage)) {
        echo  '<div class="ui error message">
            <strong>错误：' . $errorMessage . '</strong> 
            </div>';
        }
        ?>

        <div class="ui grid container" style="max-width: none !important;">
            <?php
            if ($theme == 'fold') {
            ?>
            <div class="four wide column">
                <div class="ui vertical pointing menu">
                    <?php
                    $methodTotal = 0;
                    foreach ($allApiS as $item) {
                        $methodTotal += count($item['methods']);
                    }
                    ?>
                    <div class="item"><h4>接口服务列表&nbsp;(<?php echo $methodTotal; ?> )</h4></div>
                    <?php
                    $num = 0;
                    foreach ($allApiS as $key => $item) {
                        ?>
                        <a class="item <?php if ($num == 0) {
                            echo 'active';
                        } ?>" data-tab="<?php echo $key; ?>"><?php echo $item['title']; ?> </a>
                        <?php
                        $num++;
                    }
                    ?>
                    <?php
                    if ($num > 12) {
                        echo '<a class="item" href="#menu_top">返回顶部↑↑↑</a>';
                    }
                    ?>
                </div>
            </div>
            <?php } ?> <!-- 折叠时的菜单 -->

            <!-- 折叠时与展开时的布局差异 -->
            <?php if ($theme == 'fold') { ?>
            <div class="twelve wide stretched column">
            <?php } else { ?>
            <div class="wide stretched column">
            <?php 
                    // 展开时，将全部的接口服务，转到第一组
                    $mergeAllApiS = array('all' => array('methods' => array()));
                    foreach ($allApiS as $key => $item) {
                        foreach ($item['methods'] as $mKey => $mItem) {
                            $mergeAllApiS['all']['methods'][$mKey] = $mItem;
                        }
                    }
                    $allApiS = $mergeAllApiS;
            } 
            ?>

                <?php
                $uri  = $env ? '' : str_ireplace('listAllApis.php', 'checkApiParams.php', $_SERVER['REQUEST_URI']);
                $num2 = 0;
                foreach ($allApiS as $key => $item) {
                    ?>
                    <div class="ui  tab <?php if ($num2 == 0) { ?>active<?php } ?>" data-tab="<?php echo $key; ?>">
                        <table
                            class="ui red celled striped table <?php echo $table_color_arr[$num2 % count($table_color_arr)]; ?> celled striped table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>接口服务</th>
                                <th>接口名称</th>
                                <th>更多说明</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $num = 1;
                            foreach ($item['methods'] as $mKey => $mItem) {
                                if ($env){
                                    ob_start ();
                                    $_REQUEST['service'] = $mItem['service'];
                                    include('checkApiParams.php');
                                    $string = ob_get_clean ();
                                    saveHtml ( $mItem['service'], $string);
                                    $link = $mItem['service'] . '.html';
                                }else{
                                    $link = $uri . '?service=' . $mItem['service'];
                                }
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$mItem['service']}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                    <!-- 主题切换，仅当在线时才支持 -->
                    <?php
                            if (!$env) {
                                $curUrl = $_SERVER['SCRIPT_NAME'];
                                if ($theme == 'fold') {
                                    echo '<div style="float: right"><a href="' . $curUrl . '?type=expand">切换回展开版</a></div>';
                                } else {
                                    echo '<div style="float: right"><a href="' . $curUrl . '?type=fold">切换回折叠版</a></div>';
                                }
                            }
                    ?>

                    </div>
                    <?php
                    $num2++;
                }
                ?>


            </div>
        </div>
        <div class="ui blue message">
            <strong>温馨提示：</strong> 此接口服务列表根据后台代码自动生成，可在接口类的文件注释的第一行修改左侧菜单标题。
        </div>
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi <?php echo PHALAPI_VERSION; ?></a> <p>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('.pointing.menu .item').tab();
    $('.ui.sticky').sticky();
	//当点击跳转链接后，回到页面顶部位置
    $(".pointing.menu .item").click(function() {
        $('body,html').animate({
                scrollTop: 0
            },
            500);
        return false;
    });
</script>

</body>
</html>
<?php
if ($env){
    $string = ob_get_clean ();
    saveHtml ( 'index', $string);
    $str = "
Usage:

生成展开版：  php {$argv[0]} expand
生成折叠版：  php {$argv[0]} fold

脚本执行完毕！离线文档保存路径为：";
    if (strtoupper ( substr ( PHP_OS, 0,3)) == 'WIN'){
        $str = iconv ( 'utf-8', 'gbk', $str);
    }
    $str .= $root . DIRECTORY_SEPARATOR . 'doc' ;
    echo $str, PHP_EOL, PHP_EOL;
    exit(0);
}
