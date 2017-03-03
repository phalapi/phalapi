<?php
/**
 * PhalApi Online API List Document (auto generated)
 *
 * - list all of APIs from classes which are with prefix Api_
 * - sort by service name
 * - support multi folder
 * 
 * <br>Usage:<br>
```
 * <?php
 * class Api_Demo extends PhalApi_Api {
 *      /**
 *       * 1.1 TODO: write your service name here
 *       * /
 *      public function index() {
 *          // TODO: do something ...    
 *      }
 * }
 *
```
 * @license     http://www.phalapi.net/license GPL
 * @link        http://www.phalapi.net/
 * @author      xiaoxunzhao 2015-10-25
 * @modify      Aevit, dogstar <chanzonghuang@gmail.com> 2014-10-29
 * @modify      shwy, Aevit, dogstar <1179758693@qq.com> 2017-03-02
 */

define("D_S", DIRECTORY_SEPARATOR);
$root = dirname(__FILE__);

/**
 * Project to scan

 * TODO: API project folder name, rename it in need
 */
$apiDirName = 'Demo';

/**
 * Library to scan

 * TODO: Add the path of libraries, such as ```Library/XXX/XXX```
 */
$libraryPaths = array(
    'Library/User/User',    // User Library
    'Library/Auth/Auth',    // Auth Library
);

// init
require_once implode(D_S, array($root, '..', 'init.php'));

// scan project
DI()->loader->addDirs($apiDirName);
$files = listDir(implode(D_S, array($root, '..', '..', $apiDirName, 'Api')));

// and scan library
foreach ($libraryPaths as $aPath) {
    $toAddDir = str_replace('/', D_S, $aPath);
    DI()->loader->addDirs($toAddDir);

    $toListDir = API_ROOT . D_S . $toAddDir . D_S . 'Api';
    $aLibFiles = listDir($toListDir);

    $files = array_merge($files, $aLibFiles);
}

// exclude methods
$allPhalApiApiMethods = get_class_methods('PhalApi_Api');

$allApiS = array();

foreach ($files as $value) {
    $value    = realpath($value);
    $subValue = substr($value, strpos($value, D_S . 'Api' . D_S) + 1);
    // support multi folder, unlimited
    $arr                = explode(D_S, $subValue);
    $subValue           = implode(D_S, $arr);
    $apiServer          = str_replace(array(D_S, '.php'), array('_', ''), $subValue);
    $apiServerShortName = substr($apiServer, 4);

    if (!class_exists($apiServer)) {
        continue;
    }

    // menu title on the left
    $ref        = new ReflectionClass($apiServer);
    $title      = "// please check comments at api file($apiServer)";
    $desc       = '// please use @desc annotation';
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

        $title = '// please check method annotation';
        $desc = '// please use @desc annotation';
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
// sort
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

$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $apiDirName; ?> | Online API List Document | PhalApi</title>
    <link href="//cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.css" rel="stylesheet">
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>
<br/>


<div class="ui text container" style="max-width: none !important; width: 1200px">
    <div class="ui floating message">
        <div class="ui grid container" style="max-width: none !important;">
            <div class="four wide column">
                <div class="ui vertical pointing menu">
                    <div class="item"><h4>API LIST</h4></div>
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

                </div>
            </div>
            <div class="twelve wide stretched column">
                <?php
                $uri  = str_ireplace('listAllApis.php', 'checkApiParams.php', $_SERVER['REQUEST_URI']);
                $num2 = 0;
                foreach ($allApiS as $key => $item) {
                    ?>
                    <div class="ui  tab <?php if ($num2 == 0) { ?>active<?php } ?>" data-tab="<?php echo $key; ?>">
                        <table
                            class="ui red celled striped table <?php echo $table_color_arr[$num2 % count($table_color_arr)]; ?> celled striped table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>API Service</th>
                                <th>Service Name</th>
                                <th>More Details</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $num = 1;
                            foreach ($item['methods'] as $mKey => $mItem) {
                                $link = $uri . '?service=' . $mItem['service'];
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$mItem['service']}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                    </div>
                    <?php
                    $num2++;
                }
                ?>


            </div>
        </div>
        <div class="ui blue message">
            <strong>NOTE:</strong> This document is generated by PhalApi automatically. You can change the menu title in related API file.
        </div>
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi <?php echo PHALAPI_VERSION; ?></a> <p>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('.pointing.menu .item').tab();
    $('.ui.sticky').sticky();
</script>

</body>
</html>
