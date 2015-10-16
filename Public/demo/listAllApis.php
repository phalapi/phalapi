<?php
$root = dirname(__FILE__);

/**
 * 项目的文件夹名
 */
$apiDirName = 'Demo';
/**
 * 不参与到接口文档中的文件名 例：['.','..','Test.php']那么Test.php中的方法将不被列出
 * 注：. 和 .. 是必须得有的！！！！！
 */
$prevention = ['.','..'];

require_once $root . '/../init.php';
DI()->loader->addDirs([$apiDirName]);
$files = scandir($root.'/../../'.$apiDirName.'/Api');
$files = array_diff($files, $prevention);
foreach( $files as $value ){
    $ApiServer = rtrim($value,'.php');
    $Method = array_diff(get_class_methods('Api_'.$ApiServer), get_class_methods('PhalApi_Api'));
    foreach( $Method as $MValue ){
        $rMethod = new ReflectionMethod('Api_'.$ApiServer, $MValue);
        $docComment = $rMethod->getDocComment();
        if( $docComment != false ){
            $docCommentArr = explode("\n", $docComment);
            $comment = trim($docCommentArr[1]);
            $desc = substr($comment, strpos($comment, '*') + 1);
        }else{
            $desc = '请检测函数注释';
        }
        $description[$ApiServer.'.'.$MValue] = $desc;
        $ApiS[] = $ApiServer.'.'.$MValue;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>freeApi - 接口列表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<br />
<div class="container">
    <div class="page-header">
        <h1>用户模块</h1>
    </div>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>#</th><th>接口服务</th><th>接口名称</th><th>更多说明</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach( $ApiS as $KK => $VV ){
            $NO = $KK + 1;
            echo "<tr><td>{$NO}</td><td><a href='checkApiParams.php?service={$VV}' target='_blank'>{$VV}</a></td><td>{$description[$VV]}</td><td></td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
