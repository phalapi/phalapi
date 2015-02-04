#PhalApi - 轻量级PHP后台接口开发框架 - V1.1.0


###PhalApi是一个轻量级PHP后台接口开发框架，目的是让接口开发更简单。
```
如果您决定采用此框架进行接口项目开发，请邮件（chanzonghuang@gmail.com）知会我一下，谢谢 ^_^
```
#背景
过去十年，是互联网时代；如今的十年，是移动时代。  
  
在iOS、Android、Windows Phone、PC版、Web版等各种终端和各种垂直应用不停更新迭代的大背景下，显然很是需要一组乃至一系列稳定的后台接口支撑。
接口，是如此重要，正如Jaroslav Tulach在《软件框架设计的艺术》一书中说的：
```
API就如同恒星，一旦出现，便与我们永恒共存。
```

所以，这里希望通过提供一个快速可用的后台接口开发框架，可以：

```
一来，支撑各业务场景下接口开发；
二来，阐明如何进行接口开发、设计和维护，以很好支持海量访问、大数据、向前向后兼容等；
三来，顺便分享一些好的思想、技巧和有用的工具、最佳实践。
```

如果您有接口项目开发的需要，又刚好需要一个PHP接口框架，欢迎使用！    我们也争取致力于将我们的PhalApi维护成像恒星一样：  
```
不断更新，保持生气；为接口负责，为开源负责！
```


#安装
+ *请从release分支拉取稳定的代码*
+ *推荐在Linux服务器上进行开发*
+ *建议PHP >= 5.3.3*

将代码下载解压到服务器后即可，然后把根目录设置为Public。如nginx下：

```
root   /.../PhalApi/Public;
```

为验证是否安装成功，可以访问默认接口服务，如：http://localhost/PhalApi/demo/，正常时会返回类如：
```
{
    "ret": 200,
    "data": {
        "title": "Default Api",
        "content": "PHPer您好，欢迎使用PhalApi！",
        "version": "1.1.0",
        "time": 1422779027
    },
    "msg": ""
}
```
#在线体验
```
//默认的接口服务
http://phalapi.oschina.mopaas.com/Public/demo/

//带参数的示例接口
http://phalapi.oschina.mopaas.com/Public/demo/?service=Default.Index&username=oschina

//故意请求一个非法的服务
http://phalapi.oschina.mopaas.com/Public/demo/?service=Demo.None
{
    "ret": 400,
    "data": [],
    "msg": "非法请求：服务Demo.None不存在"
}
```

#文档
###后台接口开发就是如此简单，Write the code, enjoy yourself !

更多信息，请访问：[PhalApi - 轻量级PHP后台接口开发框架 - 让接口开发更简单](http://my.oschina.net/u/256338/blog/363288)  

#[酷！]接口参数在线查询
为了方便客户端查看最新的接口参数，特别提供此在线工具，根据接口代码实时生成接口参数报表，完全不需要后台开发编写维护额外的文档。我觉得，这很符合敏捷开发之道。
```
//接口参数在线查询工具链接
http://phalapi.oschina.mopaas.com/Public/demo/checkApiParams.php
```
如：http://phalapi.oschina.mopaas.com/Public/demo/checkApiParams.php ，访问效果如下：

 ![mahua](http://static.oschina.net/uploads/space/2015/0130/190225_8HRX_256338.jpg)
 因此，接口所需要的参数，对于接口开发人员，也只是简单配置一下参数规则，便可以轻松获取。
 
#[赞！]接口单元测试
不能被测试的代码，不是好代码。
在使用此框架进行接口开发时，我们强烈建议使用测试驱动开发，以便于不断积累形成接口测试体系，保证接口向前向后兼容。  
如下，是对接口 **/?service=User.GetBaseInfo&userId=1** 进行单元测试时，按： **构造-操作-检验（BUILD-OPERATE-CHECK）模式** ，即：  

```
    /**
     * @group testGetBaseInfo
     */ 
    public function testGetBaseInfo()
    {
        $str = 'service=User.GetBaseInfo&userId=1';
        parse_str($str, $params);

        DI()->request = new PhalApi_Request($params);

        $api = new Api_User(); 
        //自己进行初始化
        $api->initialize();
        $rs = $api->getBaseInfo();

        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('info', $rs);

        $this->assertEquals(0, $rs['code']);

        $this->assertEquals('dogstar', $rs['info']['name']);
        $this->assertEquals('oschina', $rs['info']['from']);
    }
```
更多请参考：[最佳开发实践：自动化单元测试 - PHP](http://my.oschina.net/u/256338/blog/370605)
#主要目录结构
```
.
│
├── PhalApi         //PhalApi框架，后期可以整包升级
│
│
├── Public          //对外访问目录，建议隐藏PHP实现
│   └── demo        //Demo服务访问入口
│
│
├── Config          //项目接口公共配置，主要有：app.php, sys.php, dbs.php
├── Data            //项目接口公共数据
├── Language        //项目接口公共翻译
├── Runtime         //项目接口运行文件目录，用于存放日记，可软链到别的区
│
│
└── Demo            //应用接口服务，名称自取，可多组
    ├── Api             //接口响应层
    ├── Domain          //接口领域层
    ├── Model           //接口持久层
    └── Tests           //接口单元测试

```
#更新日记
以下更新日记，主要是为了说明，我们一直在努力更新和维护。
###2015-02-02    版本1.1.0    一个全新的开始！
```
此版本在原来的基础上进行了大量的重构和更多的规范统一，主要有：
1、分离框架代码和项目代码，便于以后框架升级；
2、统一全部的入口文件，以便不同的版本、终端、入口和测试环境使用，并隐藏PHP语言实现；
3、框架代码统一从原来的Core_改名为PhalApi_，并且把PhalApi_DI::one()统一为快速函数的写法：DI()；
4、重新界定应用项目的代码目录结构，以包的形式快速开发；
5、全部文档相应更新；
//注意！此版本不兼容旧的写法，如有问题，请与我联系。
```
###2015-02-01
```
1、正常时，ret返回调整为：200，原来为0；
2、异常统一简化为两大类：客户端非法请求400、服务端运行错误500；
3、日记文件写入重构，并将权限更改为777，以便不同场合下日记写入时的permission denied；
4、单元测试整理；
```
###2015-01-31
```
1、参数规则的解析，移除不必要的固定类型，以及addslashes转换，单元测试整理；
2、参数规则文档编写：http://my.oschina.net/u/256338/blog/372947
```
###2015-01-29
```
1、examples代码重新整理，及入门文档同步更新；
2、入口文件的调整；
```

###2015-01-28
```
1、补充入门开发示例的文档，及相关的测试代码和产品代码，主要是examples；
2、提供接口参数在线查询工具；
```
###2015-01-24
```
1、PhalApi开源；
```