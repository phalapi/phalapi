<?php
namespace App\Common;

/**
 * API文档生成类
 * - 可根据项目需要，自行调整。
 * @author dogstar <chanzonghuang@gmail.com> 2026-01-29
 */
class ApiDoc {
    
    /**
     * 生成markdown格式接口文档
     * @desc 可用于生成AI提示词，告诉AI如何调用API接口；也可以用于导出md接口文档。
     */
    public static function generateMarkdown($projectName, $apiHost, $service, $methods, $version, $description, $descComment, $rules, $returns, $exceptions) {
        $apiHost = $apiHost ? $apiHost : '`你的API接口域名`';
        $apiHost = rtrim($apiHost, '/');
        $description = strip_tags($description);
        $descComment = strip_tags($descComment);
        $version = $version ?: '1.0.0';
        $methods = $methods ?: 'POST';

        $sysParamsMd = "| s | string | 必填 | 接口服务名，固定为`{$service}` |
";
        $paramsMd = "";
        $paramsData = [];

        foreach ($rules as $it) {
            if (in_array($it['name'], ['s', 'service'])) {
                continue;
            }
            $arr = [];
            $arr[] = $it['name'];
            $arr[] = !empty($it['type']) ? $it['type'] : 'string';
            $arr[] = !empty($it['require']) ? '必填' : '可选';
            $arr[] = str_replace("\n", " ", strip_tags($it['desc']));

            $paramsMd .= "| " . implode(" | ", $arr) . " |\n";
            $paramsData[$it['name']] = $it['default'] ?? '';
        }

        $returnsMd = "| ret	| int | 接口状态码，`200`表示成功，`4xx`表示客户端非法请求，`5xx`表示服务端异常 |
| data | object/array/混合 | 接口返回的业务数据，由不同的API接口决定不同的数据返回字段和结构。|
| msg | 字符串 | 提示信息，面向技术人员的帮助或错误提示信息，成功返回时为空字符串 |
";
        foreach ($returns as $it) {
            $arr = [];
            $arr[] = 'data.' . $it[1];
            $arr[] = $it[0];
            $arr[] = strip_tags($it[2]);
            $returnsMd .= "| " . implode(" | ", $arr)  . "|\n";
        }

        // 拼接curl的用法示例（判断是GET或POST）
        $curlExample = '';
        if (strtoupper($methods) === 'GET') {
            $curlExample = "curl -X GET '{$apiHost}/?s={$service}'" . (!empty($paramsData) ? '&' . http_build_query($paramsData) : '') . " \\\n";
            $curlExample .= "  -H 'Content-Type: application/json'";
        } else {
            $curlExample = "curl -X POST '{$apiHost}/?s={$service}' \\\n";
            $curlExample .= "  -H 'Content-Type: application/json' \\\n";
            $curlExample .= "  -d '" . json_encode($paramsData, JSON_UNESCAPED_UNICODE) . "'";
        }

        // 读取demo示例
        $demoJson = "";
        $demoPath = API_ROOT . '/src/view/docs/demos';
        // 公共前缀部分
        $prefixCode = '';
        $prefixFile = $demoPath . '/_prefix.json';
        if (file_exists($prefixFile)) {
            $prefixCode = file_get_contents($prefixFile);
        }
        $codeFile = $demoPath . '/' . $service . '.json';
        if (file_exists($codeFile)) {
            $demoJson = $prefixCode . file_get_contents($codeFile);
        }
        

        $md = "
# 【{$description}】接口文档 v{$version}
**所属平台**: {$projectName}
**接口地址**: `{$apiHost}/?s={$service}`
**请求方式**: {$methods}

## 接口基本信息
- **功能描述**: {$descComment}
- **返回格式**: JSON

## 请求参数说明

### 系统参数
| 参数名 | 类型 | 必填 | 说明 |
|---|---|---|---|
{$sysParamsMd}

### 业务参数
| 参数名 | 类型 | 必填 | 说明 |
|---|---|---|---|
{$paramsMd}

## 返回字段
| 返回字段 | 类型 | 说明 |
|---|---|---|
{$returnsMd}

## 请求示例
```bash
{$curlExample}
```

返回示例：
```json
{$demoJson}
```";

        return $md;
    }
}