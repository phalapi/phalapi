<?php
/**
 * 工具 - 查看接口参数规则
 */

require_once dirname(__FILE__) . '/../init.php';

//装载你的接口
DI()->loader->addDirs('Demo');

$service = DI()->request->get('service', 'Default.Index');

$rules = array();

$typeMaps = array(
    'string' => '字符串',
    'int' => '整型',
    'float' => '浮点型',
    'boolean' => '布尔型',
    'date' => '日期',
    'array' => '数组',
    'fixed' => '固定值',
    'enum' => '枚举类型',
    'object' => '对象',
);

try {
    $api = PhalApi_ApiFactory::generateService(false);
    $rules = $api->getApiRules();
} catch (PhalApi_Exception $ex){
    $service .= ' - ' . $ex->getMessage();
}

list($className, $methodName) = explode('.', $service);
$className = 'Api_' . $className;

$rMethod = new ReflectionMethod($className, $methodName);
$docComment = $rMethod->getDocComment();
$docCommentArr = explode("\n", $docComment);

$description = '';
$returnArr = array();

foreach ($docCommentArr as $comment) {
	$comment = trim($comment);
	//var_dump($comment);
    if (empty($description) && strpos($comment, '@') === false && strpos($comment, '/') === false) {
        $description = substr($comment, strpos($comment, '*') + 1);
        continue;
    }

    $pos = stripos($comment, '@return');
    if ($pos === false) {
        continue;
    }

    $returnCommentArr = explode(' ', substr($comment, $pos + 8));
    if (count($returnCommentArr) < 2) {
        continue;
    }
    if (!isset($returnCommentArr[2])) {
        $returnCommentArr[2] = '';	//可选的字段说明
    }
    $returnArr[] = $returnCommentArr; 
}

/** ---------------- 页面输出 ---------------- **/

echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>接口参数在线查询</title>

    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!-- Bootstrap -->
    <!-- <link href="/css/bootstrap.min.css" rel="stylesheet"> -->
</head>

<body>

<br /> 

<div class="container">

<div class="jumbotron">

EOT;

echo "<h2>接口：$service</h2><br/><p>$description</p><br/>";

echo <<<EOT
<h3>接口参数</h3>
<table class="table table-striped" >
<thead>
<tr><th>参数名字</th><th>类型</th><th>是否必须</th><th>默认值</th><th>其他</th></tr>
EOT;

foreach ($rules as $key => $rule) {
    $name = $rule['name'];
    if (!isset($rule['type'])) {
        $rule['type'] = 'string';
    }
    $type = isset($typeMaps[$rule['type']]) ? $typeMaps[$rule['type']] : $rule['type'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">必须</font>' : '可选';
    $default = isset($rule['default']) ? $rule['default'] : '';

    $other = '';
    if (isset($rule['min'])) {
        $other .= ' 最小：' . $rule['min'];
    }
    if (isset($rule['max'])) {
        $other .= ' 最大：' . $rule['max'];
    }
    if (isset($rule['range'])) {
        $other .= ' 范围：' . implode('/', $rule['range']);
    }

    echo "<tr><td>$name</td><td>$type</td><td>$require</td><td>$default</td><td>$other</td></tr>\n";
}

echo <<<EOT
</table>

<br>

<h3>返回结果</h3>
<table class="table table-striped" >
<thead>
<tr><th>返回字段</th><th>类型</th><th>说明</th></tr>
EOT;

foreach ($returnArr as $item) {
	$name = $item[1];
	$type = isset($typeMaps[$item[0]]) ? $typeMaps[$item[0]] : $item[0];
	$detail = $item[2];
	
	echo "<tr><td>$name</td><td>$type</td><td>$detail</td></tr>";
}

echo <<<EOT

</table>

<br/>

    <div role="alert" class="alert alert-info">
      <strong>温馨提示：</strong> 此接口参数列表根据后台代码自动生成，可将 ?service= 改成您需要查询的接口/服务
    </div>
    
</div>

</div> <!-- /container -->

</body>
</html>
EOT;

