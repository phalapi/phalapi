# CLI扩展类库

此类库可用于开发命令行应用，基于[GetOpt.PHP](https://github.com/getopt-php/getopt-php)，主要作用是将命令参数进行解析和处理。  
  
## 安装

执行 ```composer require phalapi/cli```，或 在项目的composer.json文件中，添加：  
```json
{
    "require": {
        "phalapi/cli": "^3.0"
    }
}
```

配置好后，再执行```composer update```更新操作即可。 

## 编写你的命令行入口文件
创建以下的CLI入口文件，保存到：./bin/cli.php 文件：  

```php
<?php
// 引入PhalApi初始化文件
require_once dirname(__FILE__) . '/../public/init.php';

// 以CLI命令行方式运行接口
$cli = new PhalApi\CLI\Lite();
$cli->response();
```
  
## 运行和使用

### (1) 查看使用说明
不提供任何参数，执行脚本，会看到提示：  
```bash
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息


缺少service参数，请使用 -s 或 --service 指定需要调用的API接口。
```

### (2) 正常运行
默认接口服务使用```service```名称，缩写为```s```，如运行命令：  

```bash
$ php ./bin/cli.php -s Site.Index --username dogstar
{"ret":200,"data":{"title":"Hello PhalApi","version":"2.0.1","time":1501079142},"msg":""}
```
  
### (3) 获取帮助
指定接口服务service后，即可使用 ```-h``` 或 ```--help``` 参数以查看接口帮助信息，如：  
```bash
$ php ./bin/cli.php -s Examples_CURD.Get -h
Usage: ./cli [options] [operands]
Options:
  -s, --service <arg>     接口服务
  -h, --help              查看帮助信息
  --id <arg>              ID
```

### (4) 异常情况
异常时，将显示异常错误提示信息，以及帮助信息。

