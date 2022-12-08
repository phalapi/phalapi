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
创建以下的CLI入口文件，保存到：```./bin/phalapi-cli`` 文件：  

```php
<?php
// 引入PhalApi初始化文件
require_once dirname(__FILE__) . '/../public/init.php';

// 以CLI命令行方式运行接口
$cli = new PhalApi\CLI\Lite();
$cli->response();
```
  
## 执行接口

如果未指定需要运行的接口服务名称，将会得到以下的使用说明提示：      
```bash
$ ./bin/phalapi-cli
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息


Service:   
缺少service参数，请使用 -s 或 --service 指定需要调用的API接口。
```

## 运行效果

以 App.Hello.World 接口为例，执行方式如下：  

```
$ ./bin/phalapi-cli -s App.Hello.World   

Service: App.Hello.World
{
    "ret": 200,
    "data": {
        "content": "Hello World!"
    },
    "msg": ""
}
```

> 温馨提示：为方便查看接口执行结果，进行了JOSN美化格式输出显示。  

如果想查看帮助提示信息，可以在指定了接口服务后，使用```-h```参数。例如：  

```bash
$ ./bin/phalapi-cli -s App.Examples_Rule.SexEnum -h
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息
  --sex [<ENUM>]       性别，female为女，male为男。
```

如果缺少必要的接口参数，则会进行提示。例如：  
```bash
$ php ./bin/phalapi-cli --service App.User_User.Register
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息
  --username <arg>     必须；账号，账号需要唯一
  --password <arg>     必须；密码
  --avatar [<arg>]     默认 ；头像链接
  --sex [<INT>]        默认 0；性别，1男2女0未知
  --email [<arg>]      默认 ；邮箱
  --mobile [<arg>]     默认 ；手机号


Service: App.User_User.Register
缺少username参数，请使用 --username 指定：账号，账号需要唯一
```


> 温馨提示：phalapi-cli 会对接口参数的类型、是否必须、默认值等进行说明和提示。      

## 截图效果  

![20221208-174039](https://user-images.githubusercontent.com/12585518/206418256-59df5a90-8707-465f-b93d-f7e4db09d938.png)


## 扩展

### 扩展帮助说明  

如果需要定制你的命令脚本的帮助说明，可以重载```PhalApi\CLI\Lite::getHelpText($text)```方法。例如，修改```./bin/phalapi-cli```脚本，改为： 
 
```php
#!/usr/bin/env php
<?php
require_once dirname(__FILE__) . '/../public/init.php';

class MyCLI extends PhalApi\CLI\Lite {

    // 自定义帮助说明
    protected function getHelpText($text) {
        // 在原有的帮助说明，后面追加自己的文字  
        $context = "--- 自定义的帮助说明 ---" . PHP_EOL;
        
        return $text . PHP_EOL . $context;
    }
}

$cli = new MyCLI();
$cli->response();

```


执行后效果是：  
```bash
$ php ./bin/phalapi-cli
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息


--- 自定义的帮助说明 ---

Service: 
缺少service参数，请使用 -s 或 --service 指定需要调用的API接口
```

### 扩展接口命令列表

可以重载扩展 ```PhalApi\CLI\Lite::getServiceList()```方法。返回一个数组，在里面配置：  
```
array(
  编号 => array('service接口服务名称', '功能说明'),
)
```

例如，  
```php
class MyCLI extends PhalApi\CLI\Lite {

    // 提供接口列表，service -> 接口功能说明
    protected function getServiceList() {
        return array(
            1 => ['App.Hello.World', '演示接口'],
        );
    }
}

```

运行效果是：  
```bash
$ ./bin/phalapi-cli
Usage: ./bin/phalapi-cli [options] [operands]

Options:
  -s, --service <arg>  接口服务
  -h, --help           查看帮助信息


--- 自定义的帮助说明 ---

Service: 
1)  App.Hello.World       演示接口

缺少service参数，请使用 -s 或 --service 指定需要调用的API接口。
```

然后，可以使用快速编号执行对应的接口命令，如：  
```bash
$ ./bin/phalapi-cli -s 1

Service: App.Hello.World
{
    "ret": 200,
    "data": {
        "content": "Hello World!"
    },
    "msg": ""
}
```

### 扩展公共命令参数  

可以加工处理以下方法：  
```
    // 完成命令行参数获取后的操作，方便追加公共参数
    protected function afterGetOptions($options) {
        return $options;
    }
```

## 参考和依赖  

phalapi/cli使用了[GetOpt.PHP](https://github.com/getopt-php/getopt-php)进行命令参数的获取的解析。  

关于更多关于php处理命令行参数，或者需要定制自己和升级命令行处理的参数格式，可以参考[GetOpt.php的官方文档-Example](http://getopt-php.github.io/getopt-php/example.html)。   

