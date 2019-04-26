# CLI扩展类库

此类库可用于开发命令行应用，基于GetOpt，主要作用是将命令参数进行解析和处理。  
  
## 安装

在项目的composer.json文件中，添加：  
```
{
    "require": {
        "phalapi/cli": "dev-master"
    }
}
```

配置好后，执行composer update更新操作即可。 

## 编写命令行入口文件
参考原来的入口文件index.php，编写以下的CLI入口文件，保存到：./public/cli 文件：  

```
#!/usr/bin/env php
<?php
require_once dirname(__FILE__) . '/init.php';

$cli = new PhalApi\CLI\Lite();
$cli->response();
```
  
## 运行和使用

### (1)正常运行
默认接口服务使用```service```名称，缩写为```s```，如运行命令：  

```
$ ./cli -s Site.Index --username dogstar
{"ret":200,"data":{"title":"Hello PhalApi","version":"2.0.1","time":1501079142},"msg":""}
```
  
### (2) 获取帮助
指定接口服务service后，即可使用 --help 参数以查看接口帮助信息，如：  
```
$ ./cli -s Examples_CURD.Get --help
Usage: ./cli [options] [operands]
Options:
  -s, --service <arg>     接口服务
  -h, --help              查看帮助信息
  --id <arg>              ID
```

### (3) 异常情况
异常时，将显示异常错误提示信息，以及帮助信息。
