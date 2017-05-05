![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

## [PhalApi（π框架） - V1.4.0](http://www.phalapi.net)  
PhalApi，简称π框架，是一个PHP轻量级开源接口框架，专注于接口开发，致力让接口开发更简单。它：  
+ 致力于快速、稳定、持续交付有价值的接口服务
+ 关注于测试驱动开发、领域驱动设计、极限编程、敏捷开发
+ 有众多的扩展类库，与更多开源项目一起提供高效便捷的解决方案
+ 支持HTTP、SOAP和RPC协议，可用于快速搭建微服务、RESTful接口或Web Services
   
PhalApi代码开源、产品开源、思想开源，请放心使用。更多请访问[PhalApi官网](http://www.phalapi.net)。再次感谢**开源中国**、各位贡献者和同学。  
  
> RELATED: [PhalApi English Version at release-en](https://github.com/phalapi/phalapi/tree/release-en)。

### 导读
本文内容主要分为三部分，即：  
+ PART 1：安装、在线体验与在线接口文档
+ PART 2：快速开发、单元测试、目录结构、SDK包和Library扩展类库
+ PART 3：背景回顾、贡献者、许可与更新日记

更多请访问[在线文档](http://www.phalapi.net/wikis/)。  

## 1-1、安装
+ 请从release分支拉取发布版本的代码
+ 推荐部署于Linux服务器
+ 建议PHP >= 5.3.3

将代码下载解压到服务器后，打开浏览器，访问安装向导（推荐使用nginx，并将根目录设置为```Public```）：  
```
http://localhost/PhalApi/Public/install/
```
  
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20151024155002.jpg)  


访问默认接口服务，验证是否安装成功：  
```
http://localhost/PhalApi/Public/demo/
```
![](http://webtools.qiniudn.com/20150211_default_index.jpg)  
更多其他创建项目的方式，请访问[创建一个自己的项目](http://www.phalapi.net/wikis/%5B1.2%5D-%E5%88%9B%E5%BB%BA%E4%B8%80%E4%B8%AA%E8%87%AA%E5%B7%B1%E7%9A%84%E9%A1%B9%E7%9B%AE.html)。  

### 框架升级与框架共享
我们会尽最大的努力保证完美兼容性的升级。当框架有新版本需要升级时，只需要简单一步：更新替换```./PhalApi/PhalApi```核心框架目录即可。  
  
如果需要多个项目共享使用PhalApi框架，可以把```./PhalApi/PhalApi```移到任何位置，然后对应修改```./PhalApi/Public/init.php```文件中引入路径即可（但会导致部分脚本命令不可用）。如：  
```
require_once API_ROOT . '/path/to/PhalApi/PhalApi.php';
```

## 1-2、在线体验

 + 默认的接口服务：  

```
http://demo.phalapi.net/
```

 + 带参数的示例接口：

```
http://demo.phalapi.net/?service=Default.Index&username=oschina

{
    "ret": 200,
    "data": {
        "title": "Hello World!",
        "content": "oschina您好，欢迎使用PhalApi！",
        "version": "1.3.4",
        "time": 1473863280
    },
    "msg": ""
}
```

 + 故意请求一个非法的服务：

```
http://demo.phalapi.net/?service=Demo.None

{
    "ret": 400,
    "data": [],
    "msg": "非法请求：服务Demo.None不存在"
}
```

## 1-3、在线接口文档（自动生成）
按框架指定的格式完成接口代码编写后，PhalApi会自动生成在线接口列表文档和在线接口详情文档，以方便客户端实时查看最新的接口签名和返回字段。   
  
### (1) 在线接口列表文档  
访问对应项目路径下的```listAllApis.php```可查看此项目下全部的接口服务，如访问：  
```
http://demo.phalapi.net/listAllApis.php
```
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20160914230528.jpg)

### (2) 在线接口详情文档  
访问对应项目路径下的```checkApiParams.php```，并传递```?service=xxx.xxx```参数即可查看具体的接口文档，如访问：
```
http://demo.phalapi.net/checkApiParams.php?service=Default.Index
```
![mahua](http://7xiz2f.com1.z0.glb.clouddn.com/index20160728224002.jpg)

## 2-1、快速开发
### (1) 编写一个Hello World!接口  
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
### (2) 访问接口  
接口访问的格式为：```接口域名 + 入口路径 + ?service=XXX.XXX```，此示例中对应的链接为：
```
http://localhost/Public/demo/?service=Welcome.Say
```
### (3) 接口返回  
结果默认以JSON格式返回，即：
```
{"ret":200,"data":{"title":"Hello World!"},"msg":""}
```

### (4) 运行截图  
![](http://webtools.qiniudn.com/20150111.jpg)

## 2-2、接口单元测试
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
  

## 2-3、主要目录结构
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

## 2-4、基于接口查询语言（ASL）的SDK包支持
目前已提供的SDK有：  
 + [JAVA版](http://www.phalapi.net/wikis/%5B6.2%5D-SDK%E5%8C%85%EF%BC%88JAVA%E7%89%88%EF%BC%89.html)
 + [Objective-c版](http://www.phalapi.net/wikis/%5B6.4%5D-SDK%E5%8C%85%EF%BC%88object-c%E7%89%88%EF%BC%89.html)
 + [PHP版](http://www.phalapi.net/wikis/%5B6.3%5D-SDK%E5%8C%85%EF%BC%88PHP%E7%89%88%EF%BC%89.html)
 + C#版
 + JS版
 + Golang版
 + React-Native版
 + [Ruby版](http://www.phalapi.net/wikis/%5B6.6%5D-SDK%E5%8C%85%EF%BC%88Ruby%E7%89%88%EF%BC%89.html)
 + Python版

基于接口查询语言，可用一句话来描述接口请求，如JAVA的请求示例：  
```
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Default.Index")          //接口服务
       .withParams("username", "dogstar")     //接口参数
       .withTimeout(3000)                     //接口超时
       .request();
```
  
## 2-5、PhalApi-Library扩展类库
PhalApi框架扩展类库，致力于与开源项目一起提供高效便捷的解决方案，更多请查看：[PhalApi-Library](http://git.oschina.net/dogstar/PhalApi-Library)。

## 3-1、背景回顾
过去十年，是互联网时代；如今的十年，是移动时代。  
  
在iOS、Android、Windows Phone、PC版、Web版等各种终端和各种垂直应用不停更新迭代的大背景下，显然很是需要一组乃至一系列稳定的后台接口支撑。接口，显然是如此重要，正如Jaroslav Tulach在《软件框架设计的艺术》一书中说的：_API就如同恒星，一旦出现，便与我们永恒共存。_    

所以，这里希望通过提供一个快速可用的后台接口开发框架，可以：  
+ 一来，支撑轻量级项目后台接口的快速开发；  
+ 二来，阐明如何进行接口开发、设计和维护，以很好支持海量访问、大数据、向前向后兼容等；  
+ 三来，顺便分享一些好的思想、技巧和有用的工具、最佳实践。  
  
如果您有接口项目开发的需要，又刚好需要一个PHP接口框架，欢迎使用！我们也致力于将PhalApi维护成像恒星一样：**不断更新，保持生气；为接口负责，为开源负责！**  

## 3-2、加入我们
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
  
## 3-3、许可
PhalApi是开源框架，承诺永远免费，使用GPL协议，更多请访问[许可](http://www.phalapi.net/license)。  

## 3-4、更新日记
此 [更新日记](http://www.phalapi.net/wikis/%5B5.6%5D-%E6%9B%B4%E6%96%B0%E6%97%A5%E8%AE%B0.html) ，主要是为了说明，我们一直在努力更新和维护。
