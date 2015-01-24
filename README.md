#PhalApi轻量级PHP后台接口开发框架 


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

###后台接口开发就是如此简单，Let's start from here and enjoy yourself !

更多信息，请访问：http://my.oschina.net/u/256338/blog/363288