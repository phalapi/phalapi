<?php
/**
 * PhalApi接口列表 - 自动生成
 *
 * - 对Api_系列的接口，进行罗列
 * - 按service进行字典排序
 * - 支持多级目录扫描
 * 
 * <br>使用示例：<br>
```
 * <?php
 * class Api_Demo extends PhalApi_Api {
 *
 *      /**
 *       * 1.1 可在这里输入接口的服务名称
 *       * /
 *      public function index() {
 *          // todo ...    
 *      }
 * }
 *
```
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      xiaoxunzhao 2015-10-25
 * @modify      Aevit, dogstar <chanzonghuang@gmail.com> 2014-10-29
 */

define("D_S", DIRECTORY_SEPARATOR);
$root = dirname(__FILE__);

/**
 * 项目的文件夹名 - 如有需要，请更新此值
 */
$apiDirName = 'Demo';

require_once implode(D_S, array($root, '..', 'init.php'));
DI()->loader->addDirs($apiDirName);
$files = listDir(implode(D_S, array($root, '..', '..', $apiDirName, 'Api')));
$allPhalApiApiMethods = get_class_methods('PhalApi_Api');

$allApiS = array();

foreach ($files as $value) {
    $value = realpath($value);
    $subValue = substr($value, strpos($value, D_S . 'Api' . D_S) + 1);
    //支持多层嵌套，不限级
    $arr       = explode(D_S, $subValue);
	$subValue  = implode(D_S, $arr);
    $apiServer = str_replace(array(D_S, '.php'), array('_', ''), $subValue);

    if (!class_exists($apiServer)) {
        continue;
    }

    $method = array_diff(get_class_methods($apiServer), $allPhalApiApiMethods);

    foreach ($method as $mValue) {
        $rMethod = new Reflectionmethod($apiServer, $mValue);
        if (!$rMethod->isPublic()) {
            continue;
        }

        $title = '//请检测函数注释';
        $desc = '//请使用@desc 注释';
        $docComment = $rMethod->getDocComment();
        if ($docComment !== false) {
            $docCommentArr = explode("\n", $docComment);
            $comment = trim($docCommentArr[1]);
            $title = trim(substr($comment, strpos($comment, '*') + 1));

            foreach ($docCommentArr as $comment) {
                $pos = stripos($comment, '@desc');
                if ($pos !== false) {
                    $desc = substr($comment, $pos + 5);
                }
            }
        }

        $service = substr($apiServer, 4) . '.' . ucfirst($mValue);
        $allApiS[$service] = array(
            'service' => $service,
            'title' => $title,
            'desc' => $desc,
        );
    }
}

//字典排列
ksort($allApiS);

function listDir($dir) {
    $dir .= substr($dir, -1) == D_S ? '' : D_S;
    $dirInfo = array();
    foreach(glob($dir.'*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v; 
        }
    }
    return $dirInfo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $apiDirName; ?> - 接口列表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<br />
<div class="container">
<div class="jumbotron">
    <div class="page-header">
        <h1>接口列表</h1>
    </div>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>#</th><th>接口服务</th><th>接口名称</th><th>更多说明</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $uri = str_ireplace('listAllApis.php', 'checkApiParams.php', $_SERVER['REQUEST_URI']);

        foreach ($allApiS as $key => $item) {
            $link = $uri . '?service=' . $item['service'];
            $NO = $num++;
            echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$item['service']}</a></td><td>{$item['title']}</td><td>{$item['desc']}</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</div>

</body>
</html>
