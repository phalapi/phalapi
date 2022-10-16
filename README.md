![apic](http://cdn7.okayapi.com/yesyesapi_20190709223344_8aadbcfdbfa297a193012c0dada32a58.jpeg)  

# [PhalApi开源接口框架 / PhalApi API Framework](https://www.phalapi.net/)   

> 读音：派框架，官网：https://www.phalapi.net/  

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi/v/stable)](https://packagist.org/packages/phalapi/phalapi)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi/downloads)](https://packagist.org/packages/phalapi/phalapi)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi/v/unstable)](https://packagist.org/packages/phalapi/phalapi)
[![License](https://poser.pugx.org/phalapi/phalapi/license)](https://packagist.org/packages/phalapi/phalapi)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/phalapi/phalapi.svg)](https://starchart.cc/phalapi/phalapi)
   
   
## 开发文档
专为PHPer准备的优雅而详细的开发文档，基本都能在文档找到你要的答案，请看：[PhalApi 2.x 开发文档](http://docs.phalapi.net/#/v2.0/)。  

## 在线示例
 + 在线接口文档：[http://demo.phalapi.net/docs.php](http://demo.phalapi.net/docs.php)
 + 接口详情文档（以默认接口为例）：[http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold](http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold)
 + 默认接口服务：[http://demo.phalapi.net/?s=App.Site.Index](http://demo.phalapi.net/?s=App.Site.Index)
 
## 快速安装

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

## 部署

### Nginx配置
如果使用的是Nginx，可参考以下配置。  
```nginx
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

## 使用

### 调用接口

在PhalApi，你可以通过service参数（短名字是s参数）指定需要调用的接口服务。例如，访问默认接口服务。  

```
http://dev.phalapi.net/?s=App.Site.Index
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

运行效果，截图如下：  

![_20190201151943](https://user-images.githubusercontent.com/12585518/52108414-e98d0980-2634-11e9-9e68-9c3fae304a46.png)

### 查看在线接口文档

PhalApi会根据你编写的接口的参数配置和代码注释，自动实时生成在线接口文档。在线接口文档链接为：  
 
 + 在线接口文档：[http://dev.phalapi.net/docs.php](http://dev.phalapi.net/docs.php)  

浏览效果类似如下：  
![](http://cdn7.okayapi.com/yesyesapi_20200310225952_d319cc197a31f8f3522a82643bf31d60.png)  

接口文档详情页效果类似如下：  
支持在线接口测试、请求示例说明、生成离线版HTML接口文档、实时更新。  
![](http://cd8.okayapi.com/yesyesapi_20210713093959_d5581323d74a1191d0f5a2d1056b5087.png)  

## 翻译

修改```./public/init.php```文件，可设置当前语言。  
```php
// 翻译语言包设定-简体中文
\PhalApi\SL('zh_cn');

// Setting language to English
\PhalApi\SL('en');
```

## 一张图告诉你如何使用PhalApi 2.x
![phalapi-install](https://user-images.githubusercontent.com/12585518/52995681-4ae71200-3456-11e9-8d00-065a42cf4382.gif)

## 2020视频教程
 + [B站首发，第一课~第十一课](http://docs.phalapi.net/#/v2.0/video_1)  

## 子项目
 + [phalapi/kernal](https://github.com/phalapi/kernal)框架内核
 + [phalapi/notorm](https://github.com/phalapi/notorm)数据库包

## PhalApi composer 扩展
 + [phalapi/auth](https://github.com/twodayw/auth.git)Auth权限扩展
 + [phalapi/cli](https://github.com/phalapi/cli)CLI扩展类库
 + [phalapi/fast-route](https://github.com/phalapi/fast-route)FastRoute快速路由
 + [phalapi-aliyun-oss](https://github.com/vivlong/phalapi-aliyun-oss)PhalApi-OSS阿里云OSS包
 + [phalapi/PHPMailer](https://github.com/phalapi/PHPMailer)基于PHPMailer的邮件发送
 + [phalapi/qiniu](https://github.com/phalapi/qiniu)七牛云存储接口调用
 + [phalapi/qrcode](https://github.com/phalapi/qrcode)PhalApi 二维码扩展
 + [phalapi/pinyin](https://github.com/phalapi/pinyin)PhalApi 2.x 拼音扩展
 + [phalapi/jwt](https://github.com/twodayw/phalapi2-jwt)基于PhalApi2的JWT拓展
 + [chenall/phalapi-weixin](https://github.com/chenall/phalapi-weixin)微信扩展
 + [phalapi/wechatmini](https://github.com/JamesLiuquan/wechatmini)微信小程序扩展
 + [phalapi/ding-com-bot](https://gitee.com/kaihangchen_admin/DingComBot)钉钉企业内部webhook机器人扩展
 + [phalapi-pay](https://github.com/phalapi/pay)支持微信支付和支付宝支付

> 温馨提示：以上扩展需要先通过composer安装再使用。更多扩展类库的使用和开发，请参考文档：[PhalApi框架扩展类库](http://docs.phalapi.net/#/v2.0/library)。 

## PhalApi应用插件
 + [运营平台插件](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/phalapi_portal.zip)
 + [应用用户插件](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/phalapi_user.zip)
 + [加密解密插件](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/crypt_decrypt.zip)
 + [支付宝插件](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/phalapi_alipay.zip)
 + [茶店微信小程序应用](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/phalapi_mini_tea_ALL.zip)
 + [在线接口文档主题包](https://gitee.com/dogstar/PhalApi-Net/blob/master/download/plugins/phalapi-theme-magician.zip)

> 温馨提示：应用插件和composer扩展的区别在于，应用插件颗粒度更大，功能更具体，可能不仅有数据库、接口、界面、还可能配合其他终端，并且不受composer的规范约束，是PhalApi自主发明和设计的开发方式。更多请参考：[第三方应用插件开发教程](http://docs.phalapi.net/#/v2.0/how-to-dev-plugin)。   

## 推荐产品
 + [接口大师-即刻搭建您的接口开放平台(原名：PhalApi专业版)](http://pro.phalapi.net/)
 + [果创云-后端低代码开发平台](http://yesapi.cn/)  
 + [YesDev协作云-在线协作你的全部项目](https://www.yesdev.cn/)  

> 温馨提示：以上产品均使用了PhalApi开源框架，并为官方自主研发的产品，欢迎个人/团队/企业使用。  

## 还有问题，怎么办？ 

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/phalapi/issues)。  
如果喜欢，请帮忙在[Github](https://github.com/phalapi/phalapi)或[码云](https://gitee.com/dogstar/PhalApi)给个Star，也可以对PhalApi进行[捐赠](https://www.phalapi.net/donate.html)哦 ^_^。  

## 开源许可协议 / Licence
Apache 2.0，Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，同样鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再发布（作为开源或商业软件）。  

*由 广州果创网络科技有限公司 长期维护升级。*  


