![apic](http://webtools.qiniudn.com/master-LOGO-20150410_50.jpg)  

## [PhalApi - V1.3.6 - English Version](http://www.phalapi.net)  
PhalApi, π Framework for short, is a light-weight framework which focus on how to develop API faster and simpler. It:    
+ aims to continuous deliver available API services rapidly and stabely.  
+ foucus on TDD, DDD, XP and alige development.  
+ has many libraries, which can use optionaly according your projects need.  
+ supports HTTP, SOAP and RPC protocol, can be used to build micro services, RESTful APIs and WeWeb Services.  
   
We share our codes, our product and our mind in PhalApi, please feel free to use it. For more information, check [www.phalapi.net](http://www.phalapi.net).    
  
_PhalApi中文版请访问[release分支](https://github.com/phalapi/phalapi/tree/release)。_  
    
### Tutorial
This article is composed with three parts as below:  
+ PART 1：Installation, online demo and online API doucements.  
+ PART 2：rapid development, unit tests, framework structure, SDK packages and external librareis.  
+ PART 3：backgroud review, contributors, licience and changelog.  
  
> Related: Vist [wiki](http://www.phalapi.net/wikis/en) for more details.  

## 1.1 Installation
+ download the lastest version from release-en  
+ we recommend deploy PhalApi on Linux  
+ PHP >= 5.3.3
  
Open a browser and visit Installation Tutorial after download PhalApi and unzip on your server(we recommend nginx, and set the root to ```/path/to/PhalApi/Public```):    
```
http://localhost/PhalApi/Public/install/
```
  
![](https://camo.githubusercontent.com/d25b1f008aae8e7a2ed3499000b054a1039dd5a3/687474703a2f2f6769742e6f736368696e612e6e65742f75706c6f6164732f696d616765732f323031372f303231382f3233303234365f32303566333264375f3132313032362e706e67)  


And then visit the default API service to varify installation.  
```
http://localhost/PhalApi/Public/demo/
```
![](https://camo.githubusercontent.com/52782ceaf6853a891ce306c3021c492524941d7d/687474703a2f2f6769742e6f736368696e612e6e65742f75706c6f6164732f696d616765732f323031372f303231382f3233303331365f34316139366231335f3132313032362e706e67)  
More other ways to create your project, please check [Create your project](http://www.phalapi.net/wikis/en/1.1-create-your-project.html).  

### Upgrade and share
We will do our best to keep perfect compatibility during version upgrade. When you need to upgrade PhalApi, you just need one simple step: replace ```./PhalApi/PhalApi``` with the lastest core folder. That is it!  
  
If we need to share PhalApi, we can move ```./PhalApi/PhalApi``` to anywhere, and alter the including path in ```./PhalApi/Public/init.php```(NOTE, it will affects some shells). e.g:  
```
// $ vim ./PhalApi/Public/init.php
require_once API_ROOT . '/path/to/PhalApi/PhalApi.php';
```

## 1.2 Online Demo

 + Default API service:  
```
http://demo.phalapi.net/
```

 + Demo API with params:  
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

 + Request a service not found:  
```
http://demo.phalapi.net/?service=Demo.None

{
    "ret": 400,
    "data": [],
    "msg": "no such service as Demo.None"
}
```

## 1.3 Online API Documents (Auto Generated)
Afster project APIs have been written in PhalApi specified format, PhalApi will auto generate online API list documents and online API detail documents, which can provide client developers with realtime API signature and response structure.  
   
### (1) Online API List Documents   
We can list all API services under our project by visiting the ```listAllApis.php``` in the related project, e.g:  
```
http://demo.phalapi.net/listAllApis.php
```
![](https://camo.githubusercontent.com/12b12dd88b883fcdd3ebe2e7619003c5bb46c2c9/687474703a2f2f6769742e6f736368696e612e6e65742f75706c6f6164732f696d616765732f323031372f303231382f3233303333305f38623264356564345f3132313032362e706e67)

### (2) Online API Detail Documents  
Furthermore, We can check more detail about an API servcie by visiting the ```checkApiParams.php``` with param ```?service=xxx.xxx```, e.g:  
```
http://demo.phalapi.net/checkApiParams.php?service=Default.Index
```
![mahua](https://camo.githubusercontent.com/8829812d88782945976414d60319933ebcefa1d2/687474703a2f2f6769742e6f736368696e612e6e65742f75706c6f6164732f696d616765732f323031372f303231382f3233303334375f37373461663063325f3132313032362e706e67)

## 2.1 Rapid API Development (RAD)
### (1) Hello World!
Create an API file ```./Demo/Api/Welcome.php``` with the code as below:  
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
### (2) Visit the API  
We can call an API service by visit the url as : ```host + entrance + ?service=XXX.XXX```. In the case, the url is:  
```
http://localhost/Public/demo/?service=Welcome.Say
```
### (3) API reponse  
The API will reponse with json data as :  
```
{"ret":200,"data":{"title":"Hello World!"},"msg":""}
```
### (4) Screenshot  
![](https://camo.githubusercontent.com/2e682c31137d5a8602b1317cbf01599a7f3f63c9/687474703a2f2f6769742e6f736368696e612e6e65742f75706c6f6164732f696d616765732f323031372f303231382f3233303131355f37616461343535375f3132313032362e6a706567)

## 2.2 API Unit Tests
_The code can't be tested is bad._  
  
When develop API with PhalApi, we strong recommend following TDD, in order to build an auto testing system and keep compatibility.
According to **BUILD-OPERATE-CHECK pattern**, we can create unit tests for the API ```/?service=User.GetBaseInfo&userId=1```:  
```
    /**
     * @group testGetBaseInfo
     */ 
    public function testGetBaseInfo()
    {
        //Step 1. Contruct request URL
        $str = 'service=User.GetBaseInfo&userId=1';

        //Step 2. Exec request(imitate API request)	
        $rs = PhalApi_Helper_TestRunner::go($url);

        //Step 3. Verify
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('code', $rs);
        $this->assertArrayHasKey('msg', $rs);
        $this->assertArrayHasKey('info', $rs);

        $this->assertEquals(0, $rs['code']);

        $this->assertEquals('dogstar', $rs['info']['name']);
        $this->assertEquals('oschina', $rs['info']['from']);
    }
```
Running screenshot:  
 ![](http://static.oschina.net/uploads/space/2015/0204/234130_GSJ6_256338.png)  

We have sticked to writting unit tests for the core codes in PhalApi all the time, and out code coverage is high as 96%.  
  
## 2.3 Project Structure
```
.
│
├── PhalApi         //PhalApi framework core codes, upgrade the whole folder if need
├── Library         //PhalApi external libraries, you can add any library you need
├── SDK             //PhalApi client SDK in different programming languages
│
│
├── Public          //Public entrance
│   └── demo        //Demo service entrance
│
│
├── Config          //Project common config, including: app.php, sys.php, dbs.php
├── Data            //Project data
├── Language        //Project common translation
├── Runtime         //Project runtime folder, saving logs, etc. you can change it with ls command
│
│
└── Demo            //Demo API sevices, you can rename it as you want, and creae multi projects as you want
    ├── Api             //API controller level
    ├── Domain          //API domain level
    ├── Model           //API data level
    └── Tests           //API unit tests

```

## 2.4 Client SDK Based on API Structured Query Language (ASQL)
Currently, we have these SDKs as below:  
 + [JAVA client SDK](http://www.phalapi.net/wikis/%5B6.2%5D-SDK%E5%8C%85%EF%BC%88JAVA%E7%89%88%EF%BC%89.html)
 + [Objective-c client SDK](http://www.phalapi.net/wikis/%5B6.4%5D-SDK%E5%8C%85%EF%BC%88object-c%E7%89%88%EF%BC%89.html)
 + [PHP client SDK](http://www.phalapi.net/wikis/%5B6.3%5D-SDK%E5%8C%85%EF%BC%88PHP%E7%89%88%EF%BC%89.html)
 + C# client SDK
 + Javascript client SDK
 + Golang  client SDK
 + React-Native  client SDK
 + [Ruby  client SDK](http://www.phalapi.net/wikis/%5B6.6%5D-SDK%E5%8C%85%EF%BC%88Ruby%E7%89%88%EF%BC%89.html)
 + Pythong client SDK
  
We can describe an API request as one sentence with API Structured Query Language. Take Java as example:  
```
PhalApiClientResponse response = PhalApiClient.create()
       .withHost("http://demo.phalapi.net/")
       .withService("Default.Index")          //API service
       .withParams("username", "dogstar")     //API params
       .withTimeout(3000)                     //API timeout
       .request();
```
  
## 2.5 PhalApi-Library External Libraries
More external libraries are valiable on [PhalApi-Library](https://github.com/phalapi/phalapi-library)  

## 3.1 Background Review
We provide PhalApi because we hope to :  
+ Firstly, support light-weight API projects rapid development;   
+ Secondly, explain how to design, develop and mantain APIs in face of big data;  
+ Last but not least, share some good mind, skills, tools and best practices.  
  
In summery, welcome to use PhalApi! We will keep devoting ourself into PhalApi and keep it full of energy, reponseable for open source framework!  

## 3.2 Join Us
Obviously, this is just the beginning, and there is a long way to go. PhalApi is not our (develop team) framework, but our (all of us) framework. Let's do something meaningful when we are young. Welcome to join us anytime!  
    
Contributors:  
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
  
## 3.3 License
PhalApi is an open source framework, and promises keep free forever. PhalApi is under GPL, more details on [License](http://www.phalapi.net/license).  

## 3.4 Change Logs
[Change Logs](http://www.phalapi.net/wikis/%5B5.6%5D-%E6%9B%B4%E6%96%B0%E6%97%A5%E8%AE%B0.html).  
