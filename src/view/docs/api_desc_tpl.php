<?php

// 搜索关键字
$keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
$url = $url . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
$url .= trim(substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/') + 1), '.');
$semanticPath = './semantic/'; // 本地
if (substr(PHP_SAPI, 0, 3) == 'cli') {
    $semanticPath = 'https://cdn.bootcss.com/semantic-ui/2.2.2/';
}

$whoami = \PhalApi\DI()->admin->check(false) ? \PhalApi\DI()->admin->username : \PhalApi\T('Sign In');
$suffixTitle = \PhalApi\T('Online API Docs');

$descriptionNoHtml = strip_tags($description);

echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$descriptionNoHtml} - {$service} - {$projectName} - {$suffixTitle}</title>

    <meta name="description" content="{$descComment} - {$descriptionNoHtml} - {$service} - {$projectName}。基于PhalApi开源接口框架。">
    <meta name="keywords" content="{$descriptionNoHtml},{$service},{$projectName},PhalApi">

    <link rel="stylesheet" href="{$semanticPath}semantic.min.css">
    <link rel="stylesheet" href="{$semanticPath}components/table.min.css">
    <link rel="stylesheet" href="{$semanticPath}components/container.min.css">
    <link rel="stylesheet" href="{$semanticPath}components/message.min.css">
    <link rel="stylesheet" href="{$semanticPath}components/label.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="/static/jquery.min.js"></script>
    <script src="/static/jquery.cookie.min.js"></script>
</head>

<body>
EOT;


include dirname(__FILE__) . '/api_menu.php';
?>

<div class="row" style="margin-top: 60px;" ></div>

    <div class="ui text container" style="max-width: none !important;">
        <div class="ui floating message">

    <h2 class="ui header">
      <i class="settings icon"></i>
      <div class="content">
        <?php echo $service; ?>
        <?php if (!empty($version)) { ?>
            <span class="ui label small">v <?php echo $version; ?></span>
        <?php } ?>
        <div class="sub header">
            <?php echo $description; ?>
        </div>
      </div>
    </h2>

            <h4><i class="linkify in alternate icon"></i><?php echo \PhalApi\T('API Url'); ?>：<?php echo $url; ?>?s=<?php echo $service; ?></h4>

            <div class="ui raised segment">
                <span class="ui <?php echo !empty($methods) && strtoupper($methods) == 'POST' ? 'green' : 'blue'; ?> ribbon label"><?php echo \PhalApi\T('Request Method'), '&nbsp; ', !empty($methods) ? $methods : 'GET/POST'; ?></span>
                <div class="ui message">
                <p><?php echo \PhalApi\T('API Description'); ?>：<?php echo $descComment; ?></p>
                </div>
            </div>

            <h3><i class="sign in alternate icon"></i><?php echo \PhalApi\T('API Parameters'); ?></h3>
            <table class="ui red celled striped table" >
                <thead>
                    <tr><th><?php echo \PhalApi\T('Parameter Name'); ?></th><th><?php echo \PhalApi\T('Type'); ?></th><th><?php echo \PhalApi\T('Is Required'); ?></th><th><?php echo \PhalApi\T('Default'); ?></th><th><?php echo \PhalApi\T('Note'); ?></th><th><?php echo \PhalApi\T('Description'); ?></th></tr>
                </thead>
                <tbody>
<?php
$typeMaps = array(
    'string' => '字符串',
    'int' => '整型',
    'float' => '浮点型',
    'boolean' => '布尔型',
    'date' => '日期',
    'array' => '字符串', // 转换成客户端看到的参数类型
    'fixed' => '固定值',
    'enum' => '枚举类型',
    'object' => '对象',
);
foreach ($typeMaps as $key => &$tmRef) {
    $tmRef = \PhalApi\T($key);
}

foreach ($rules as $key => $rule) {
    // 接口文档不显示
    if (!empty($rule['is_doc_hide'])) {
        continue;
    }

    $name = $rule['name'];
    if (!isset($rule['type'])) {
        $rule['type'] = 'string';
    }
    $type = isset($typeMaps[$rule['type']]) ? $typeMaps[$rule['type']] : $rule['type'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">'.\PhalApi\T('Required').'</font>' : \PhalApi\T('Optional');
    $default = isset($rule['default']) ? $rule['default'] : '';
    if ($default === NULL) {
        $default = 'NULL';
    } else if (is_array($default)) {
        // @dogstar 20190120 默认值，反序列
        $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($ruleFormat == 'explode') {
            $default = implode(isset($rule['separator']) ? $rule['separator'] : ',', $default);
        } else {
            $default = json_encode($default);
        }
    } else if (!is_string($default)) {
        $default = var_export($default, true);
    }

    // 数组类型的格式说明
    if ($rule['type'] == 'array' && in_array($rule['format'], array('json', 'explode'))) {
        $type .= sprintf(
            '<span class="ui label blue small">%s</span>',
            $rule['format'] == 'json'
            ? \PhalApi\T('json')
            : sprintf(\PhalApi\T('seperated by %s'), isset($rule['separator']) ? $rule['separator'] : ',')
        );
    }

    $other = array();
    if (isset($rule['min'])) {
        $other[] = \PhalApi\T('min: ') . $rule['min'];
    }
    if (isset($rule['max'])) {
        $other[] = \PhalApi\T('max: ') . $rule['max'];
    }
    if (isset($rule['range'])) {
        $other[] = \PhalApi\T('range: ') . implode('/', $rule['range']);
    }
    if (isset($rule['source'])) {
        $other[] = \PhalApi\T('source: ') . strtoupper($rule['source']);
    }
    $other = implode('；', $other);

    $desc = isset($rule['desc']) ? trim($rule['desc']) : '';

    echo "<tr><td>$name</td><td>$type</td><td>$require</td><td>$default</td><td>$other</td><td>$desc</td></tr>\n";
}

/**
 * 返回结果
 */
?>
                </tbody>
            </table>
            <h3><i class="sign out alternate icon"></i><?php echo \PhalApi\T('Response Result'); ?></h3>
            <table class="ui green celled striped table" >
                <thead>
                    <tr><th><?php echo \PhalApi\T('Parameter Name'); ?></th><th><?php echo \PhalApi\T('Type'); ?></th><th><?php echo \PhalApi\T('Description'); ?></th></tr>
                </thead>
                <tbody>
                
<?php
foreach ($returns as $item) {
    $name = $item[1];
    $type = isset($typeMaps[$item[0]]) ? $typeMaps[$item[0]] : $item[0];
    $detail = $item[2];

    echo "<tr><td>$name</td><td>$type</td><td>$detail</td></tr>";
}

echo <<<EOT
            </tbody>
        </table>
EOT;

/**
 * 异常情况
 */
if (!empty($exceptions)) {
    $elt = \PhalApi\T('Error List');
    $est = \PhalApi\T('Error Status');
    $edt = \PhalApi\T('Error Description');
    echo <<<EOT
            <h3 id="ret_code_list_id"><i class="bell icon"></i>{$elt}</h3>
            <table class="ui red celled striped table" >
                <thead>
                    <tr><th>{$est}</th><th>{$edt}</th>
                </thead>
                <tbody>
EOT;

    foreach ($exceptions as $exItem) {
        $exCode = $exItem[0];
        $exMsg = isset($exItem[1]) ? $exItem[1] : '';
        echo "<tr><td>$exCode</td><td>$exMsg</td></tr>";
    }

    echo <<<EOT
            </tbody>
        </table>
EOT;

/**
 * 返回结果
 */
}
?>

<h3>
    <i class="bug icon"></i><?php echo \PhalApi\T('Test Online'); ?> &nbsp;&nbsp;
</h3>


<table class="ui red celled striped table" >
    <thead>
        <tr><th width="25%"><?php echo \PhalApi\T('Parameter'); ?></th><th width="10%"><?php echo \PhalApi\T('Is Required'); ?></th><th width="65%"><?php echo \PhalApi\T('Value'); ?></th></tr>
    </thead>
    <tbody id="params">
        <tr>
            <td>service</td>
            <td><font color="red"><?php echo \PhalApi\T('Required'); ?></font></td>
            <td><div class="ui fluid input disabled"><input name="service" data-source="get" value="<?php echo $service; ?>" style="width:100%;" class="C_input" id="service" /></div></td>
        </tr>
<?php
foreach ($rules as $key => $rule){
    // 接口文档不显示
    if (!empty($rule['is_doc_hide'])) {
        continue;
    }

    $source = isset($rule['source']) ? $rule['source'] : '';
    //数据源为server和header时该参数不需要提供
    if ($source == 'server' || $source == 'header') {
        continue;
    }
    $name = $rule['name'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">'.\PhalApi\T('Required').'</font>' : \PhalApi\T('Optional');
    // 提供给表单的默认值
    $default = isset($rule['default'])
        ? (is_array($rule['default']) // 针对数组，进行反序列化
            ? (!empty($rule['format']) && $rule['format'] == 'explode' 
                ? implode(isset($rule['separator']) ? $rule['separator'] : ',', $rule['default']) 
                : json_encode($rule['default'])) 
            : $rule['default'])
        : '';
    $default = htmlspecialchars($default);
    $desc = isset($rule['desc']) ? htmlspecialchars(trim($rule['desc'])) : '';
    $inputType = (isset($rule['type']) && $rule['type'] == 'file') ? 'file' : 'text';

    $multiple = '';
    if ($inputType == 'file') {
        $multiple = 'multiple="multiple"';
    }
    echo <<<EOT
        <tr>
            <td>{$name}</td>
            <td>{$require}</td>
            <td><div class="ui fluid input"><input name="{$name}" value="{$default}" data-source="{$source}" placeholder="{$desc}" style="width:100%;" class="C_input" type="$inputType" $multiple/></div></td>
        </tr>
EOT;
}
?>
    </tbody>
</table>
<div style="display: flex;align-items:center;">
    <!--<select name="request_type" style="font-size: 14px; padding: 2px;">
        <option value="POST">POST</option>
        <option value="GET">GET</option>
    </select>-->
<!--
接口链接：&nbsp;<input name="request_url" value="{$url}" style="width:500px; height:24px; line-height:18px; font-size:13px;position:relative; padding-left:5px;margin-left: 10px"/>
    <input type="submit" name="submit" value="发送" id="submit" style="font-size:14px;line-height: 20px;margin-left: 10px " class="ui green button" />
-->

</div>

<div class="ui fluid action input">
      <input placeholder="请求的接口链接" type="text" name="request_url" value="<?php echo $url; ?>" >
      <button class="ui button <?php echo !empty($methods) && strtoupper($methods) == 'POST' ? 'green' : 'blue'; ?>" id="submit" ><?php echo \PhalApi\T('Request API'); ?></button>
</div>

<div class="ui blue message" id="json_output" style="overflow: auto;">
</div>

<h3><i class="code icon"></i><?php echo \PhalApi\T('Client Request Demo'); ?></h3>

<?php
$demoCodes = array();
$demoPath = dirname(__FILE__) . '/demos';
foreach (array(
    'json', 'curl', 'js', 'php', 'py', 'java', 'cs', 'go', 'oc',
) as $whatCode) {
    // 公共前缀部分
    $prefixCode = '';
    $prefixFile = $demoPath . '/_prefix.' . $whatCode;
    if (file_exists($prefixFile)) {
        $prefixCode = htmlspecialchars(file_get_contents($prefixFile));
    }

    $codeFile = $demoPath . '/' . $service . '.' . $whatCode;
    if (file_exists($codeFile)) {
        $demoCodes[$whatCode] = $prefixCode . htmlspecialchars(file_get_contents($codeFile));
        
        // 公共后缀部分
        $suffixCode = '';
        $suffixFile = $demoPath . '/_suffix.' . $whatCode;
        if (file_exists($suffixFile)) {
            $suffixCode = htmlspecialchars(file_get_contents($suffixFile));
            $demoCodes[$whatCode] .= $suffixCode;
        }
        
        $demoCodes[$whatCode] = str_replace(array('{url}', '{s}'), array($url, $service), $demoCodes[$whatCode]);
    }
}

if (empty($demoCodes)) {
    $demoCodes['json'] = '# 暂无示例，可添加示例文件：./src/view/docs/demos/' . $service . '.json';
}

$codeName = array(
    'json' => 'HTTP通用示例',
    'js' => 'Javascript示例',
    'oc' => 'Object-C示例',
    'java' => 'Java示例',
    'curl' => 'CURL示例',
    'php' => 'PHP示例',
    'py' => 'Python示例',
    'go' => 'Golang示例',
    'cs' => 'C#示例',
);
foreach ($codeName as $key => &$cnRef) {
    $cnRef = \PhalApi\T($key . ' demo');
}

if (!empty($demoCodes)) {
    $allKeys = array_keys($demoCodes);
    $firstLan = $allKeys[0]; // 第一个标签的语言

    $itemHtml = '';
    $segmentHtml = '';

    foreach ($demoCodes as $code => $codeStr) {
        $itemHtml .= sprintf('<a class="%s item" data-tab="%s">%s</a>', ($firstLan == $code ? 'active ' : ''), 'datatab' . $code, $codeName[$code]);
        $segmentHtml .= sprintf('<div class="ui bottom attached %s tab segment" data-tab="%s"><pre><code>%s</code></pre></div>', ($firstLan == $code ? 'active ' : ''), 'datatab' . $code, $codeStr);
    }

    echo <<<EOT

<div class="ui tab segment active" data-tab="{$firstLan}">
    <div class="ui top attached tabular menu">
        {$itemHtml}
    </div>

    {$segmentHtml}
</div>

<!-- 代码高亮 -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

EOT;
}



/**
 * 底部
 */
$version = PHALAPI_VERSION;
$thisYear = date('Y');
$tips = \PhalApi\T('Tips: ');
$helpMsg = \PhalApi\T('This API Document will be generated automately by PHP code and comments. More detail please visit <a href="{url}" target="_blank">Docs</a>.', array('url' => 'http://docs.phalapi.net/#/v2.0/api-docs'));
echo <<<EOT
        <div class="ui blue message">
          <strong>{$tips}</strong> {$helpMsg}
        </div>
        </div>

    </div>

<!-- tag -->
<script src="{$semanticPath}semantic.min.js" ></script>
<script src="{$semanticPath}components/tab.js"></script>

EOT;

include dirname(__FILE__) . '/api_footer.php';

echo <<<EOT
</body>
</html>
EOT;
?>
