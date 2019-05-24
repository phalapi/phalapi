![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

# [PhalApi 2.6.1 - 接口，从简单开始！](https://www.phalapi.net/) 

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi/v/stable)](https://packagist.org/packages/phalapi/phalapi)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi/downloads)](https://packagist.org/packages/phalapi/phalapi)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi/v/unstable)](https://packagist.org/packages/phalapi/phalapi)
[![License](https://poser.pugx.org/phalapi/phalapi/license)](https://packagist.org/packages/phalapi/phalapi)

> 为了更好的接口开发体验，2019年，重新出发！

## 1、开发文档
专为PHPer准备的优雅而详细的开发文档，请看：[PhalApi 2.x 开发文档](http://docs.phalapi.net/#/v2.0/)。  

## 2、在线示例
 + 默认接口服务：[http://demo.phalapi.net/?s=App.Site.Index](http://demo.phalapi.net/?s=App.Site.Index)
 + 在线接口文档：[http://demo.phalapi.net/docs.php](http://demo.phalapi.net/docs.php)
 + 接口详情文档（以默认接口为例）：[http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold](http://demo.phalapi.net/docs.php?service=App.Site.Index&detail=1&type=fold)
 + **PhalApi创新项目-小白接口**（免费、免开发、直接可用的的云端API）：[https://www.okayapi.com/](https://www.okayapi.com/?f=github)
 
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

## 4、使用

### 调用接口

在PhalApi，你可以通过service参数（短名字是s参数）指定需要调用的接口服务。例如，访问默认接口服务。  

 + 默认接口：[http://localhost/phalapi/public/?s=App.Site.Index](http://localhost/phalapi/public/?s=App.Site.Index)

> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。

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
 
 + 在线接口文档：[http://localhost/phalapi/public/docs.php](http://localhost/phalapi/public/docs.php)

浏览效果类似如下：  

![_20190201113515](https://user-images.githubusercontent.com/12585518/52101206-8fc91700-2615-11e9-8c4d-20e30cc264c4.png)

## 5、一张图告诉你如何使用PhalApi 2.x
![phalapi-install](https://user-images.githubusercontent.com/12585518/52995681-4ae71200-3456-11e9-8d00-065a42cf4382.gif)

## 6、子项目
 + [phalapi/kernal](https://github.com/phalapi/kernal)
 + [phalapi/notorm](https://github.com/phalapi/notorm)

## 7、还有问题，怎么办？  

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/phalapi/issues)，或进入[PhalApi开源社区](http://qa.phalapi.net/?f=github)。  
如果喜欢，请帮忙在[Github](https://github.com/phalapi/phalapi)或[码云](https://gitee.com/dogstar/PhalApi)给个Star，也可以对PhalApi进行[捐赠](https://www.phalapi.net/donate.html)哦 ^_^。
