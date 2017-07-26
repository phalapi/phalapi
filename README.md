![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

# [PhalApi 2.0.0 - 助你创造价值！](https://www.phalapi.net/) 

[![Latest Stable Version](https://poser.pugx.org/phalapi/phalapi/v/stable)](https://packagist.org/packages/phalapi/phalapi)
[![Total Downloads](https://poser.pugx.org/phalapi/phalapi/downloads)](https://packagist.org/packages/phalapi/phalapi)
[![Latest Unstable Version](https://poser.pugx.org/phalapi/phalapi/v/unstable)](https://packagist.org/packages/phalapi/phalapi)
[![License](https://poser.pugx.org/phalapi/phalapi/license)](https://packagist.org/packages/phalapi/phalapi)

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

### 访问接口服务

随后，可通过以下链接，访问默认接口服务。  
```
http://localhost/path/to/phalapi/public/
```
可以看到类似这样的输出：  
```
{
    "ret": 200,
    "data": {
        "title": "Hello PhalApi",
        "version": "2.0.1",
        "time": 1501079142
    },
    "msg": ""
}
```

运行效果，截图如下：  

![](http://7xiz2f.com1.z0.glb.clouddn.com/20170726223129_eecf3d78826c5841020364c852c35156)


> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。

更多请见：[PhalApi 2.x 开发文档](http://docs.phalapi.net/#/v2.0/)  

## 发现问题，怎么办？  

如发现问题，或者任何问题，欢迎提交Issue到[这里](https://github.com/phalapi/phalapi/issues)。
