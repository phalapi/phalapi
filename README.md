![apic](http://cdn7.okayapi.com/yesyesapi_20190709223344_8aadbcfdbfa297a193012c0dada32a58.jpeg)  

# [PhalApi 2.12.0 - 接口，从简单开始！](https://www.phalapi.net/) 

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi/v/stable)](https://packagist.org/packages/phalapi/phalapi)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi/downloads)](https://packagist.org/packages/phalapi/phalapi)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi/v/unstable)](https://packagist.org/packages/phalapi/phalapi)
[![License](https://poser.pugx.org/phalapi/phalapi/license)](https://packagist.org/packages/phalapi/phalapi)

> [PhalApi Pro 专业版 - 智慧编程，让接口开发更有趣！](http://pro.yesapi.cn/)
 
## 1、开发文档
专为PHPer准备的优雅而详细的开发文档，请看：[PhalApi 2.x 开发文档](http://docs.phalapi.net/#/v2.0/)。  

## 2、在线示例
 + 默认接口服务：[http://demo.phalapi.net/?s=App.Site.Index](http://demo.phalapi.net/?s=App.Site.Index)
 + 在线接口文档：[http://demo.phalapi.net/docs.php](http://demo.phalapi.net/docs.php)
 + 接口详情文档（以默认接口为例）：[http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold](http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold)
 + **PhalApi创新项目-小白接口**（免费、免开发、直接可用的的云端API）：[https://www.yesapi.cn/](https://www.yesapi.cn/?f=github)
 
## 3、快速安装

### composer一键安装

使用composer创建项目的命令，可实现一键安装。

```bash
$ composer create-project phalapi/phalapi
```
> 温馨提示：关于composer的使用，请参考[Composer 中文网 / Packagist 中国全量镜像](http://www.phpcomposer.com/)。

### 手动下载安装

或者，也可以进行手动安装。将此Git项目代码下载解压后，进行可选的composer更新，即：  
```bash
$ composer update
```

## 4、部署

### Nginx配置
如果使用的是Nginx，可参考以下配置。  
```
server {
    listen 80;
    server_name dev.phalapi.net;
    # 将根目录设置到public目录
    root /path/to/phalapi/public;
    charset utf-8;

    location / {
        index index.php;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        # 根据当前环境，选择合适的通讯方式
        # fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```
配置时需要将网站根目录设置到public目录，配置保存后重启nginx。

> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。

### 数据库配置
如何使用的是MySQL数据库，参考修改```./config/dbs.php```数据库配置。
```php
return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'db_master' => array(                       // 服务器标记
            'type'      => 'mysql',                 // 数据库类型，暂时只支持：mysql, sqlserver
            'host'      => '127.0.0.1',             // 数据库域名
            'name'      => 'phalapi',               // 数据库名字
            'user'      => 'root',                  // 数据库用户名
            'password'  => '',	                    // 数据库密码
            'port'      => 3306,                    // 数据库端口
            'charset'   => 'UTF8',                  // 数据库字符集
            'pdo_attr_string'   => false,           // 数据库查询结果统一使用字符串，true是，false否
            'driver_options' => array(              // PDO初始化时的连接选项配置
                // 若需要更多配置，请参考官方文档：https://www.php.net/manual/zh/pdo.constants.php
            ),
        ),
    ),

    // 更多代码省略……
);
```

最后，需要给runtime目录添加写入权限。更多安装说明请参考文档[下载与安装](http://docs.phalapi.net/#/v2.0/download-and-setup)。

## 5、使用

### 调用接口

在PhalApi，你可以通过service参数（短名字是s参数）指定需要调用的接口服务。例如，访问默认接口服务。  

 + 默认接口：[http://dev.phalapi.net/?s=App.Site.Index](http://dev.phalapi.net/?s=App.Site.Index)



对应执行的PHP代码在./src/app/Api/Site.php文件，源码片段如下：  
```php
<?php
namespace App\Api;
use PhalApi\Api;

/**
 * 默认接口服务类
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Site extends Api {
    public function getRules() {
        return array(
            'index' => array(
                'username'  => array('name' => 'username', 'default' => 'PhalApi', 'desc' => '用户名'),
            ),
        );
    }

    /**
     * 默认接口服务
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function index() {
        return array(
            'title' => 'Hello ' . $this->username,
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        );
    }
}
```

接口请求后结果输出类似如下：  
```
{
    "ret": 200,
    "data": {
        "title": "Hello PhalApi",
        "version": "2.4.2",
        "time": 1501079142
    },
    "msg": ""
}
```

运行效果，截图如下：  

![_20190201151943](https://user-images.githubusercontent.com/12585518/52108414-e98d0980-2634-11e9-9e68-9c3fae304a46.png)

### 查看在线接口文档

PhalApi会根据你编写的接口的参数配置和代码注释，自动实时生成在线接口文档。在线接口文档链接为：  
 
 + 在线接口文档：[http://dev.phalapi.net/docs.php](http://dev.phalapi.net/docs.php)

浏览效果类似如下：  

![](http://cdn7.okayapi.com/yesyesapi_20200310225952_d319cc197a31f8f3522a82643bf31d60.png)

### 进入运营平台
PhalApi采用了当前流行且优秀的layuimin开发全新的管理后台，专门提供给非技术的运营人员使用（技术人员也可使用）。

![](http://cdn7.okayapi.com/yesyesapi_20200309172737_a4b73f5763b4d8758f367a2a34230830.png)

## 6、一张图告诉你如何使用PhalApi 2.x
![phalapi-install](https://user-images.githubusercontent.com/12585518/52995681-4ae71200-3456-11e9-8d00-065a42cf4382.gif)

## 7、子项目
 + [phalapi/kernal](https://github.com/phalapi/kernal)
 + [phalapi/notorm](https://github.com/phalapi/notorm)

## 8、还有问题，怎么办？  

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/phalapi/issues)，或进入[PhalApi开源社区](http://talk.phalapi.net/?f=github)。  
如果喜欢，请帮忙在[Github](https://github.com/phalapi/phalapi)或[码云](https://gitee.com/dogstar/PhalApi)给个Star，也可以对PhalApi进行[捐赠](https://www.phalapi.net/donate.html)哦 ^_^。
