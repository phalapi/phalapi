<?php
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$service} - {$projectName} - 在线接口文档</title>

    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/table.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/container.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/message.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/label.min.css">
    <script src="http://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
</head>

<body>

<br /> 

    <div class="ui text container" style="max-width: none !important;">
        <div class="ui floating message">

EOT;

echo "<h2 class='ui header'>接口：$service</h2><br/> <span class='ui teal tag label'>$description</span>";

/**
 * 接口说明 & 接口参数
 */
echo <<<EOT
            <div class="ui raised segment">
                <span class="ui red ribbon label">接口说明</span>
                <div class="ui message">
                    <p>{$descComment}</p>
                </div>
            </div>
            <h3>接口参数</h3>
            <table class="ui red celled striped table" >
                <thead>
                    <tr><th>参数名字</th><th>类型</th><th>是否必须</th><th>默认值</th><th>其他</th><th>说明</th></tr>
                </thead>
                <tbody>
EOT;

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

foreach ($rules as $key => $rule) {
    $name = $rule['name'];
    if (!isset($rule['type'])) {
        $rule['type'] = 'string';
    }
    $type = isset($typeMaps[$rule['type']]) ? $typeMaps[$rule['type']] : $rule['type'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">必须</font>' : '可选';
    $default = isset($rule['default']) ? $rule['default'] : '';
    if ($default === NULL) {
        $default = 'NULL';
    } else if (is_array($default)) {
        $default = json_encode($default);
    } else if (!is_string($default)) {
        $default = var_export($default, true);
    }

    $other = array();
    if (isset($rule['min'])) {
        $other[] = '最小：' . $rule['min'];
    }
    if (isset($rule['max'])) {
        $other[] = '最大：' . $rule['max'];
    }
    if (isset($rule['range'])) {
        $other[] = '范围：' . implode('/', $rule['range']);
    }
    if (isset($rule['source'])) {
        $other[] = '数据源：' . strtoupper($rule['source']);
    }
    $other = implode('；', $other);

    $desc = isset($rule['desc']) ? trim($rule['desc']) : '';

    echo "<tr><td>$name</td><td>$type</td><td>$require</td><td>$default</td><td>$other</td><td>$desc</td></tr>\n";
}

/**
 * 返回结果
 */
echo <<<EOT
                </tbody>
            </table>
            <h3>返回结果</h3>
            <table class="ui green celled striped table" >
                <thead>
                    <tr><th>返回字段</th><th>类型</th><th>说明</th></tr>
                </thead>
                <tbody>
EOT;

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
    echo <<<EOT
            <h3>异常情况</h3>
            <table class="ui red celled striped table" >
                <thead>
                    <tr><th>错误码</th><th>错误描述信息</th>
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
}

/**
 * 返回结果
 */
echo <<<EOT
<h3>
    请求模拟 &nbsp;&nbsp;
</h3>
EOT;


echo <<<EOT
<table class="ui green celled striped table" >
    <thead>
        <tr><th>参数</th><th>是否必填</th><th>值</th></tr>
    </thead>
    <tbody id="params">
        <tr>
            <td>service</td>
            <td><font color="red">必须</font></td>
            <td><input name="service" value="{$service}" style="width:100%;" class="C_input" /></td>
        </tr>
EOT;
foreach ($rules as $key => $rule){
    $name = $rule['name'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">必须</font>' : '可选';
    $default = isset($rule['default']) ? $rule['default'] : '';
    $desc = isset($rule['desc']) ? trim($rule['desc']) : '';
    $inputType = (isset($rule['type']) && $rule['type'] == 'file') ? 'file' : 'text';
    echo <<<EOT
        <tr>
            <td>{$name}</td>
            <td>{$require}</td>
            <td><input name="{$name}" value="{$default}" placeholder="{$desc}" style="width:100%;" class="C_input" type="$inputType"/></td>
        </tr>
EOT;
}
echo <<<EOT
    </tbody>
</table>
<div style="display: flex;align-items:center;">
    <select name="request_type" style="font-size: 14px; padding: 2px;">
        <option value="POST">POST</option>
        <option value="GET">GET</option>
    </select>
EOT;
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
$url = $url . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
$url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
echo <<<EOT
&nbsp;<input name="request_url" value="{$url}" style="width:500px; height:24px; line-height:18px; font-size:13px;position:relative; padding-left:5px;margin-left: 10px"/>
    <input type="submit" name="submit" value="发送" id="submit" style="font-size:14px;line-height: 20px;margin-left: 10px "/>
</div>
EOT;

/**
 * JSON结果
 */
echo <<<EOT
<div class="ui blue message" id="json_output">
</div>
EOT;

/**
 * 底部
 */
$version = PHALAPI_VERSION;
echo <<<EOT
        <div class="ui blue message">
          <strong>温馨提示：</strong> 此接口参数列表根据后台代码自动生成，可将 ?service= 改成您需要查询的接口/服务
        </div>
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi {$version}</a><span id="version_update"></span></p>
        </div>
    </div>
    <script type="text/javascript">
        function getData() {
            var data={};
            $("td input").each(function(index,e) {
                if ($.trim(e.value)){
                    data[e.name] = e.value;
                }
            });
            return data;
        }
        
        $(function(){
            $("#json_output").hide();
            $("#submit").on("click",function(){
                $.ajax({
                    url:$("input[name=request_url]").val(),
                    type:$("select").val(),
                    data:getData(),
                    success:function(res,status,xhr){
                        console.log(xhr);
                        var statu = xhr.status + ' ' + xhr.statusText;
                        var header = xhr.getAllResponseHeaders();
                        var json_text = JSON.stringify(res, null, 4);    // 缩进4个空格
                        $("#json_output").html('<pre>' + statu + '<br/>' + header + '<br/>' + json_text + '</pre>');
                        $("#json_output").show();
                    },
                    error:function(error){
                        console.log(error)
                    }
                })
            })

            checkLastestVersion();
        })

        // 检测最新版本
        function checkLastestVersion() {
                $.ajax({
                    url:'https://www.phalapi.net/check_lastest_version.php',
                    type:'get',
                    data:{version : '$version'},
                    success:function(res,status,xhr){
                        if (!res.ret || res.ret != 200) {
                            return;
                        }
                        if (res.data.need_upgrade >= 0) {
                            return;
                        }          

                        $('#version_update').html('&nbsp; | &nbsp; <a target="_blank" href=" ' + res.data.url + ' "><strong>免费升级到 PhalApi ' + res.data.version + '</strong></a>');              
                    },
                    error:function(error){
                        console.log(error)
                    }
                })

        }
    </script>
</body>
</html>
EOT;


