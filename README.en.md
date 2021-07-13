![apic](http://cdn7.okayapi.com/yesyesapi_20190709223344_8aadbcfdbfa297a193012c0dada32a58.jpeg)  

# [PhalApi开源接口框架 / PhalApi API Framework](https://www.phalapi.net/)   

> 读音：派框架

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi/v/stable)](https://packagist.org/packages/phalapi/phalapi)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi/downloads)](https://packagist.org/packages/phalapi/phalapi)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi/v/unstable)](https://packagist.org/packages/phalapi/phalapi)
[![License](https://poser.pugx.org/phalapi/phalapi/license)](https://packagist.org/packages/phalapi/phalapi)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/phalapi/phalapi.svg)](https://starchart.cc/phalapi/phalapi)
   
   
## 开发文档 / Documents
专为PHPer准备的优雅而详细的开发文档，请看：[PhalApi 2.x 开发文档](http://docs.phalapi.net/#/v2.0/)。  
[PhalApi 2.x English Docs](http://docs-en.phalapi.net/#/v2.0/).  

## 在线示例 / Demo
 + 默认接口服务：[http://demo.phalapi.net/?s=App.Site.Index](http://demo.phalapi.net/?s=App.Site.Index)
 + 在线接口文档：[http://demo.phalapi.net/docs.php](http://demo.phalapi.net/docs.php)
 + 接口详情文档（以默认接口为例）：[http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold](http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold)
 
## 快速安装 / Install

### composer一键安装 / Install by composer

使用composer创建项目的命令，可实现一键安装。  

One-click installation can be achieved by using the command of composer to create a project.  

```bash
$ composer create-project phalapi/phalapi
```
> 温馨提示：关于composer的使用，请参考[Composer 中文网 / Packagist 中国全量镜像](http://www.phpcomposer.com/)。  

### 手动下载安装 / Download and Install manually

或者，也可以进行手动安装。将此Git项目代码下载解压后，进行可选的composer更新，即：  
Alternatively, manual installation is also possible. Download PhalApi Project master-2x branch Source code. After downloading and unzipping, perform an optional composer update:  
```bash
$ composer update
```

## 部署 / Deployment

### Nginx配置 / Nginx Configuration
如果使用的是Nginx，可参考以下配置。  
If you are using Nginx, you can refer to the following configuration.  
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
Point the root path of the visit to public folder. Save and reload nginx.  

> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。  
> Tips: It is recommended to point the root path of the visit to /path/to/phalapi/public.

### 数据库配置 / Database Configuration
如何使用的是MySQL数据库，参考修改```./config/dbs.php```数据库配置。  
If you are using MySQL, please edit ```./config/dbs.php```.  

```php
return array(
    /**
     * DB数据库服务器集群 / database cluster
     */
    'servers' => array(
        'db_master' => array(                       // 服务器标记 / database identify
            'type'      => 'mysql',                 // 数据库类型，暂时只支持：mysql, sqlserver / database type
            'host'      => '127.0.0.1',             // 数据库域名 / database host
            'name'      => 'phalapi',               // 数据库名字 / database name
            'user'      => 'root',                  // 数据库用户名 / database user
            'password'  => '',	                    // 数据库密码 / database password
            'port'      => 3306,                    // 数据库端口 / database port
            'charset'   => 'UTF8',                  // 数据库字符集 / database charset
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
At last, add writeable permission to folder ```runtime```. For more detail about installation, refer to [Download and Installation](https://docs-en.phalapi.net/#/v2.0/download-and-setup).   

## 使用 / Usage

### 调用接口 / API Request

在PhalApi，你可以通过service参数（短名字是s参数）指定需要调用的接口服务。例如，访问默认接口服务。  

For PhalApi, the default communicate protocol is HTTP/HTTPS. According to the specific implementation of the API service, we could use GET or POST to request. By default, you can specify the ```service``` parameter or ```s``` for short when requesting. The default API service is ```App.Site.Index```.    

 + 默认接口：[http://dev.phalapi.net/?s=App.Site.Index](http://dev.phalapi.net/?s=App.Site.Index)  
 + Default API：[http://dev.phalapi.net/?s=App.Site.Index](http://dev.phalapi.net/?s=App.Site.Index)  



对应执行的PHP代码在./src/app/Api/Site.php文件，源码片段如下：  
The source PHP code of ```App.Site.Index``` API service is at ```./src/app/Api/Site.php``` file.  

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
API result as below after requesting:    
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
Runtime Sreenshot:  

![_20190201151943](https://user-images.githubusercontent.com/12585518/52108414-e98d0980-2634-11e9-9e68-9c3fae304a46.png)

### 查看在线接口文档 / Visit Online API List Documents

PhalApi会根据你编写的接口的参数配置和代码注释，自动实时生成在线接口文档。在线接口文档链接为：  
PhalApi will generate realtime online API documents automatically by PHP code and PHP comments. You can visit them by:  
 
 + 在线接口文档：[http://dev.phalapi.net/docs.php](http://dev.phalapi.net/docs.php)  
 + Online API Docs：[http://dev.phalapi.net/docs.php](http://dev.phalapi.net/docs.php)  

浏览效果类似如下：  
Preview:  
![](http://cdn7.okayapi.com/yesyesapi_20200310225952_d319cc197a31f8f3522a82643bf31d60.png)  

![](http://cdn7.okayapi.com/yesyesapi_20200417145333_e8096f41f0ac10dfcd337fad4fdebfdb.png)  

## 翻译 / i18n  

修改```./public/init.php```文件，可设置当前语言。  
Edit ```./public/init.php``` file to set current language.  
```php
// 翻译语言包设定-简体中文
\PhalApi\SL('zh_cn');

// Setting language to English
\PhalApi\SL('en');
```

## 一张图告诉你如何使用PhalApi 2.x / All in One Picture
![phalapi-install](https://user-images.githubusercontent.com/12585518/52995681-4ae71200-3456-11e9-8d00-065a42cf4382.gif)

## 子项目 / Sub Projects
 + [phalapi/kernal](https://github.com/phalapi/kernal)框架内核 / Framework Kernal  
 + [phalapi/notorm](https://github.com/phalapi/notorm)数据库包 / Database Library based on NotORM  

## 还有问题，怎么办？/ Any Question?  

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/phalapi/issues)，或进入[PhalApi开源社区](http://talk.phalapi.net/?f=github)。  
如果喜欢，请帮忙在[Github](https://github.com/phalapi/phalapi)或[码云](https://gitee.com/dogstar/PhalApi)给个Star，也可以对PhalApi进行[捐赠](https://www.phalapi.net/donate.html)哦 ^_^。  

Welcome to report any issue [here](https://github.com/phalapi/phalapi/issues).   
If you like PhalApi, welcome to give us a Star at [Github](https://github.com/phalapi/phalapi).  

## 开源许可协议 / Licence
Apache 2.0，Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，同样鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再发布（作为开源或商业软件）。

