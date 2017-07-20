![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

# [PhalApi 2.0.0 - 助你创造价值！](https://www.phalapi.net/) 

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi2/v/stable)](https://packagist.org/packages/phalapi/phalapi2)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi2/downloads)](https://packagist.org/packages/phalapi/phalapi2)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi2/v/unstable)](https://packagist.org/packages/phalapi/phalapi2)
[![License](https://poser.pugx.org/phalapi/phalapi2/license)](https://packagist.org/packages/phalapi/phalapi2)

## 快速安装

### composer一键安装

使用composer创建项目的命令，可实现一键安装。如安装到phalapi2目录：

```bash
$ composer create-project phalapi/phalapi2
```
> 温馨提示：关于composer的使用，请参考[Composer 中文网 / Packagist 中国全量镜像](http://www.phpcomposer.com/)。

### 手动下载安装

或者，也可以进行手动安装。将此Git项目代码下载解压后，进行可选的composer更新，即：  
```bash
$ composer update
```

### 访问接口服务

随后，可通过以下链接，访问默认接口服务。  
```
http://localhsot/path/to/phalapi2/public/
```
可以看到类似这样的输出：  
```
{
    "ret": 200,
    "data": {
        "title": "Hello World!",
        "content": "PHPer您好，欢迎使用PhalApi！",
        "version": "2.0.0",
        "time": 1499477583
    },
    "msg": ""
}
```

> 温馨提示：推荐将访问根路径指向/path/to/phalapi2/public。

更多请见：[PhalApi 2.x 开发文档](https://github.com/phalapi/PhalApi2/wiki)  

## 发现问题，怎么办？  

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/PhalApi2/issues)。
