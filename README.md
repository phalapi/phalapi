![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

##[PhalApi（π框架） - V1.3.5](http://www.phalapi.net)  
PhalApi, π Framework for short, is a light-weight framework which focus on how to develop API faster and simpler. It:    
+ aims to continuous deliver available API services rapidly and stabely.  
+ foucus on TDD, DDD, XP and alige development.  
+ has many libraries, which can use optionaly according your projects need.  
+ supports HTTP, SOAP and RPC protocol, can be used to build micro services, RESTful APIs and WeWeb Services.  
   
We share our codes, our product and our mind in PhalApi, please feel free to use it. For more information, check [www.phalapi.net](http://www.phalapi.net).    
 
###Tutorial
This article is composed with three parts as below:  
+ PART 1：Installation, online demo and online API doucements.  
+ PART 2：rapid development, unit tests, framework structure, SDK packages and external librareis.  
+ PART 3：backgroud review, contributors, licience and changelog.  
  
Vist [wiki](http://www.phalapi.net/wikis/en) for more details.  

##1-1, Installation
+ download the lastest version from release-en  
+ we recommend deploy PhalApi on Linux  
+ PHP >= 5.3.3
  
Open a browser and visit Installation Tutorial after download PhalApi and unzip on your server(we recommend nginx, and set the root to ```/path/to/PhalApi/Public```):    
```
http://localhost/PhalApi/Public/install/
```
  
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20151024155002.jpg)  


And then visit the default API service to varify installation.  
```
http://localhost/PhalApi/Public/demo/
```
![](http://webtools.qiniudn.com/20150211_default_index.jpg)  
More other ways to create your project, please check [Create your project](http://www.phalapi.net/wikis/en/1.1-create-your-project.html).  

###Upgrade and share
We will do our best to keep perfect compatibility during version upgrade. When you need to upgrade PhalApi, you just need one simple step: replace ```./PhalApi/PhalApi``` with the lastest core folder. That is it!  
  
If we need to share PhalApi, we can move ```./PhalApi/PhalApi``` to anywhere, and alter the including path in ```./PhalApi/Public/init.php```(NOTE, it will affects some shells). e.g:  
```
// $ vim ./PhalApi/Public/init.php
require_once API_ROOT . '/path/to/PhalApi/PhalApi.php';
```

##1-2, Online demo
1, Default API service:  
```
http://demo.phalapi.net/
```
2, Demo API with params:  
```
http://demo.phalapi.net/?service=Default.Index&username=github

{
    "ret": 200,
    "data": {
        "title": "Hello World!",
        "content": "Hello github, Welcome to use PhalApi!",
        "version": "1.3.4",
        "time": 1473863280
    },
    "msg": ""
}
```
3, Request a service not found:  
```
http://demo.phalapi.net/?service=Demo.None

{
    "ret": 400,
    "data": [],
    "msg": "no such service as Demo.None"
}
```

##1-3, Online API documents(auto generated)
Afster project APIs have been written in PhalApi specified format, PhalApi will auto generate online API list documents and online API detail documents, which can provide client developers with realtime API signature and response structure.  
   
1, Online list documents   
We can list all API services under our project by visiting the ```listAllApis.php``` in the related project, e.g:  
```
http://demo.phalapi.net/listAllApis.php
```
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20160914230528.jpg)
2, Online API detail documents  
Furthermore, We can check more detail about an API servcie by visiting the ```checkApiParams.php``` with param ```?service=xxx.xxx```, e.g:  
```
http://demo.phalapi.net/checkApiParams.php?service=Default.Index
```
![mahua](http://7xiz2f.com1.z0.glb.clouddn.com/index20160728224002.jpg)

##2-1、快速开发
1、编写一个Hello World!接口  
以下代码需要放置到接口类文件```./Demo/Api/Welcome.php```中：  
```
<?php
class Api_Welcome extends PhalApi_Api {

	public function say() {
		$rs = array();
		$rs['title'] = 'Hello World!';
		return $rs;
	}
}
```
2、访问接口  
接口访问的格式为：```接口域名 + 入口路径 + ?service=XXX.XXX```，此示例中对应的链接为：
```
http://localhost/Public/demo/?service=Welcome.Say
```
3、接口返回  
结果默认以JSON格式返回，即：
```
{"ret":200,"data":{"title":"Hello World!"},"msg":""}
```
4、运行截图  
![](http://webtools.qiniudn.com/20150111.jpg)

##2-2、接口单元测试
_不能被测试的代码，不是好代码。_   
  
在使用此框架进行接口开发时，我们强烈建议使用测试驱动开发（TDD），以便于不断积累形成接口测试体系，保证接口向前向后兼容。例如对接口```/?service=User.GetBaseInfo&userId=1```进行单元测试时，按：**构造-操作-检验（BUILD-OPERATE-CHECK）模式**，即：  
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
  

##2-3、主要目录结构
```
.
│
├── PhalApi         //PhalApi框架，后期可以整包升级
├── Library         //PhalApi扩展类库，可根据需要自由添加扩展
├── SDK             //PhalApi提供的SDK包，客户可根据需要选用
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

以下为一示例目录结构图解：
![](http://7qnay5.com1.z0.glb.clouddn.com/QQ-20151015214456.jpg)   

##2-4、基于接口查询语言（ASL）的SDK包支持
目前已提供的SDK有：  
 + [JAVA版](http://www.phalapi.net/wikis/%5B6.2%5D-SDK%E5%8C%85%EF%BC%88JAVA%E7%89%88%EF%BC%89.html)
 + [Objective-c版](http://www.phalapi.net/wikis/%5B6.4%5D-SDK%E5%8C%85%EF%BC%88object-c%E7%89%88%EF%BC%89.html)
 + [PHP版](http://www.phalapi.net/wikis/%5B6.3%5D-SDK%E5%8C%85%EF%BC%88PHP%E7%89%88%EF%BC%89.html)
 + C#版
 + JS版
 + Golang版
 + React-Native版
 + [Ruby版](http://www.phalapi.net/wikis/%5B6.6%5D-SDK%E5%8C%85%EF%BC%88Ruby%E7%89%88%EF%BC%89.html)

基于接口查询语言，可用一句话来描述接口请求，如JAVA的请求示例：  
```
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Default.Index")          //接口服务
       .withParams("username", "dogstar")     //接口参数
       .withTimeout(3000)                     //接口超时
       .request();
```
  
##2-5、PhalApi-Library扩展类库
PhalApi框架扩展类库，致力于与开源项目一起提供高效便捷的解决方案，更多请查看：[PhalApi-Library](http://git.oschina.net/dogstar/PhalApi-Library)。

##3-1、背景回顾
过去十年，是互联网时代；如今的十年，是移动时代。  
  
在iOS、Android、Windows Phone、PC版、Web版等各种终端和各种垂直应用不停更新迭代的大背景下，显然很是需要一组乃至一系列稳定的后台接口支撑。接口，显然是如此重要，正如Jaroslav Tulach在《软件框架设计的艺术》一书中说的：_API就如同恒星，一旦出现，便与我们永恒共存。_    

所以，这里希望通过提供一个快速可用的后台接口开发框架，可以：  
+ 一来，支撑轻量级项目后台接口的快速开发；  
+ 二来，阐明如何进行接口开发、设计和维护，以很好支持海量访问、大数据、向前向后兼容等；  
+ 三来，顺便分享一些好的思想、技巧和有用的工具、最佳实践。  
  
如果您有接口项目开发的需要，又刚好需要一个PHP接口框架，欢迎使用！我们也致力于将PhalApi维护成像恒星一样：**不断更新，保持生气；为接口负责，为开源负责！**  

##3-2、加入我们
显然，这只是一个开始，我们要走的路还很长。PhalApi是我们（开发团队）的框架，更是我们（所有人）的框架。在一个人还年轻的时候，我觉得，就应该着手致力做一些对社会有意义的事情，一如开源。欢迎&期待你的加入！   
  
在加入前，可先查看[致框架贡献者：加入PhalApi开源指南](http://www.phalapi.net/wikis/%5B5.8%5D-%E8%87%B4%E6%A1%86%E6%9E%B6%E8%B4%A1%E7%8C%AE%E8%80%85%EF%BC%9A%E5%8A%A0%E5%85%A5PhalApi%E5%BC%80%E6%BA%90%E6%8C%87%E5%8D%97.html)。至此，感谢以下贡献者（排名不分先后）：  
+ Aevit
+ dogstar
+ George
+ Scott
+ Summer
+ zz.guo（郭了个治浩）
+ 小艾
+ 大蝉
+ 冰霜
+ 火柴
+ 黄苗笋
+ 文振熙（喵了个咪）
+ 爱编程的小逗比
+ ... ...
  
##3-3、许可
PhalApi是开源框架，承诺永远免费，使用GPL协议，更多请访问[许可](http://www.phalapi.net/license)。  

##3-4、更新日记
此 [更新日记](http://www.phalapi.net/wikis/%5B5.6%5D-%E6%9B%B4%E6%96%B0%E6%97%A5%E8%AE%B0.html) ，主要是为了说明，我们一直在努力更新和维护。
