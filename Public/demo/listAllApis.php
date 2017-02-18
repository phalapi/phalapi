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
 *
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
 */

define("D_S", DIRECTORY_SEPARATOR);
$root = dirname(__FILE__);

/**
 * API project folder name, rename it in need
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
    // support multi folder, unlimited
    $arr       = explode(D_S, $subValue);
	$subValue  = implode(D_S, $arr);
    $apiServer = str_replace(array(D_S, '.php'), array('_', ''), $subValue);

    if (!class_exists($apiServer)) {
        continue;
    }

    $method = array_diff(get_class_methods($apiServer), $allPhalApiApiMethods);

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

// sort
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
    <title><?php echo $apiDirName; ?> | Online API List Document | PhalApi</title>
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/table.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/container.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/message.min.css">
</head>
<body>
<br />
<div class="ui text container" style="max-width: none !important;">
    <div class="ui floating message">
        <h1 class="ui header">API &nbsp;&nbsp; LIST</h1>
        <table class="ui green celled striped table">
            <thead>
                <tr>
                    <th>#</th><th>API Service</th><th>Service Name</th><th>More Details</th>
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
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi <?php echo PHALAPI_VERSION; ?></a> <p>
    </div>
</div>
</body>
</html>
