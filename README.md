#[PhalApi(π框架) - PHP轻量级开源接口框架 - V1.3.3](http://www.phalapi.net)
PhalApi，简称π框架，专注于接口开发，致力让接口开发更简单。支持HTTP、SOAP和RPC协议，可用于快速搭建微服务、RESTful接口或web services，关注于测试驱动开发、领域驱动设计、极限编程、敏捷开发以及如何快速、稳定、持续交付有价值的接口服务。
   
在此借一行文字的空间，感谢 **开源中国** 这么好的分享平台，同时也感谢您花费宝贵的时间来阅读此文档，在开源的路上，您每一次真心的关注和肯定都是我们前进的最大动力！谢谢！    
  
此框架代码开源、产品开源、思想开源，可用于个人、商业用途等，请放心使用。更多内容，请访问官网：  
![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

#背景
过去十年，是互联网时代；如今的十年，是移动时代。  
  
在iOS、Android、Windows Phone、PC版、Web版等各种终端和各种垂直应用不停更新迭代的大背景下，显然很是需要一组乃至一系列稳定的后台接口支撑。
接口，是如此重要，正如Jaroslav Tulach在《软件框架设计的艺术》一书中说的：
```
API就如同恒星，一旦出现，便与我们永恒共存。
```

所以，这里希望通过提供一个快速可用的后台接口开发框架，可以：

```
一来，支撑轻量级项目后台接口的快速开发；
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

将代码下载解压到服务器后即可，然后打开浏览器，访问安装向导（推荐使用nginx，并将根目录设置为Public）：  
```
http://localhost/PhalApi/Public/install/
```
  
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20151024155002.jpg)  


为验证是否安装成功，可以访问默认接口服务，如：http://localhost/PhalApi/Public/demo/ ，正常时会返回类如：
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
  
#框架升级
我们以最大的努力，保证完美兼容性的升级。当框架有新版本需要升级，只需要简单一步：  
```
更新替换 ./PhalApi/PhalApi 核心框架目录即可。
```

#在线体验
```
//默认的接口服务
http://demo.phalapi.net/

//带参数的示例接口
http://demo.phalapi.net/?service=Default.Index&username=oschina

//故意请求一个非法的服务
http://demo.phalapi.net/?service=Demo.None
{
    "ret": 400,
    "data": [],
    "msg": "非法请求：服务Demo.None不存在"
}
```

#文档
[http://www.phalapi.net/wikis/](http://www.phalapi.net/wikis/)  

  
#类参考手册
[http://www.phalapi.net/docs/](http://www.phalapi.net/docs/)  


#[酷！]接口参数在线查询
为了方便客户端查看最新的接口参数，特别提供此在线工具，根据接口代码实时生成接口参数报表，完全不需要后台开发编写维护额外的文档。我觉得，这很符合敏捷开发之道。
```
//接口参数在线查询工具链接
http://demo.phalapi.net/demo/checkApiParams.php
```
如：http://demo.phalapi.net/demo/checkApiParams.php ，访问效果如下：

 ![mahua](http://7qnay5.com1.z0.glb.clouddn.com/20150613.png)
 因此，接口所需要的参数，对于接口开发人员，也只是简单配置一下参数规则，便可以轻松获取。
 
#[赞！]接口单元测试
不能被测试的代码，不是好代码。 
  
在使用此框架进行接口开发时，我们强烈建议使用测试驱动开发，以便于不断积累形成接口测试体系，保证接口向前向后兼容。  
例如对接口 **/?service=User.GetBaseInfo&userId=1** 进行单元测试时，按： **构造-操作-检验（BUILD-OPERATE-CHECK）模式** ，即：  

```
    /**
     * @group testGetBaseInfo
     */ 
    public function testGetBaseInfo()
    {
        //Step 1. 构建请求URL
        $str = 'service=User.GetBaseInfo&userId=1';

        //Step 2. 执行请求（模拟接口请求）	
        $rs = PhalApi_Helper_TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('info', $rs);

        $this->assertEquals(0, $rs['code']);

        $this->assertEquals('dogstar', $rs['info']['name']);
        $this->assertEquals('oschina', $rs['info']['from']);
    }
```
运行效果：  
 ![运行效果](http://static.oschina.net/uploads/space/2015/0204/234130_GSJ6_256338.png)  
   
对于框架的核心代码，我们也一直坚持着单元测试，其核心框架代码的单元测试覆盖率可高达96%以上。
  
#[项！]基于接口查询语言的SDK包支持
可用一句话来描述接口请求，如JAVA的请求示例：
```
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Default.Index")          //接口服务
       .withParams("username", "dogstar")     //接口参数
       .withTimeout(3000)                     //接口超时
       .request();
```
  
目前已提供的SDK有：  
 + JAVA版 
 + Objective-c版
 + PHP版
 + C#版
 + JS版
  
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

以下为一示例目录结构图解
![](http://7qnay5.com1.z0.glb.clouddn.com/QQ-20151015214456.jpg)   

#加入我们
显然，这只是一个开始，我们要走的路还很长。这些也不是一个人可以完成的。即使可以，也需要很长一段时间。  
  
在一个人还年轻的时候，我觉得，就应该着手致力做一些对社会有意义的事情，因此我选择了开源。  
如果能够有机会和你一起为之努力，将会是我的荣幸，也是一段令值得兴奋激动的。SO？如果你对此深感兴趣、有激情和时间，请联系我，邮箱一如既往是：chanzonghuang@gmail.com，或者开源中国站内留言，欢迎加入，谢谢！  
  
除此之外，你也可以通过其他的方式来支持我们。一如：在你使用此框架进行实际项目开发过程中所遇到的问题或者更好的解决方案都可以反馈给我们；又如：关注和认可，因为在开源的路上，您每一次真心的关注和肯定都是我们前进的最大动力！谢谢！
  
#更新日记
此 [更新日记](http://www.phalapi.net/wikis/%5B5.6%5D-%E6%9B%B4%E6%96%B0%E6%97%A5%E8%AE%B0.html) ，主要是为了说明，我们一直在努力更新和维护。
