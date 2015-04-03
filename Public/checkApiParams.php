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
);

try {
    $api = PhalApi_ApiFactory::generateService(false);
    $rules = $api->getApiRules();
} catch (PhalApi_Exception $ex){
    $service .= ' - ' . $ex->getMessage();
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

echo "<h2>接口：$service</h2><br/>";

echo <<<EOT
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

    <div role="alert" class="alert alert-info">
      <strong>温馨提示：</strong> 此接口参数列表根据后台代码自动生成，可将 ?service= 改成您需要查询的接口/服务
    </div>

</div>

</div> <!-- /container -->

</body>
</html>
EOT;

