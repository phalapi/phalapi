#PhalApi - 轻量级PHP后台接口开发框架 


###PhalApi是一个轻量级PHP后台接口开发框架，目的是让接口开发更简单。

#安装

将代码下载解压到服务器后即可，然后把根目录设置为Public。如nginx下：

```
root   /.../PhalApi/Public;
```

为验证是否安装成功，可以访问默认接口服务，如：http://localhost/PhalApi/，正常时会返回类如：
```
{
    "ret": 0,
    "data": {
        "title": "Default Api",
        "content": "PHPer您好，欢迎使用PhalApi！",
        "verion": "1.0.0",
        "time": 1422118935
    },
    "msg": ""
}
```
#在线体验
```
//默认的接口服务
http://phalapi.oschina.mopaas.com/Public/

//带参数的示例接口
http://phalapi.oschina.mopaas.com/Public/?service=Demo.Test&username=oschina

//故意请求一个非法的服务
http://phalapi.oschina.mopaas.com/Public/?service=Demo.None

```
##接口参数在线查询
为了方便客户端查看最新的接口参数，特别提供此在线工具，根据接口代码实时生成接口参数报表，完全不需要后台开发编写维护额外的文档。我觉得，这很符合敏捷开发之道。
```
//接口参数在线查询工具链接
http://phalapi.oschina.mopaas.com/Public/helpers/checkApiParams.php
```
如：http://phalapi.oschina.mopaas.com/Public/helpers/checkApiParams.php ，访问效果如下：

 ![mahua](http://static.oschina.net/uploads/space/2015/0128/010444_ytat_256338.png)

#文档说明
###后台接口开发就是如此简单，Write the code, enjoy yourself !

更多信息，请访问：http://my.oschina.net/u/256338/blog/363288

## <br />
#/** ---------------------------- PHP黄金分割线 ---------------------------- **/
## <br />

#更新日记
##2015-01-28
```
1、补充入门开发示例的文档，及相关的测试代码和产品代码，主要是examples；
2、提供接口参数在线查询工具；
```