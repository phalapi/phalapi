
## 使用说明
将框架目录下的 ./SDK/PHP/PhalApiClient 目录中的全部代码拷贝到项目里面即可使用。  
 
## 代码示例
如下是使用的代码场景片段。  
 
首先，我们需要导入SDK包：
```php
<?php

require_once dirname(__FILE__) . '/PhalApiClient.php';

$client = PhalApiClient::create()
        ->withHost('http://demo.phalapi.net/');

$rs = $client->reset()
    ->withService('Default.Index')
    ->withParams('username', 'dogstar')
    ->withTimeout(3000)
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());
```
 
## 运行效果
运行后，可以看到：  
```php
int(200)

array(4) {
    ["title"]=>
    string(12) "Hello World!"
    ["content"]=>
    string(36) "dogstar您好，欢迎使用PhalApi！"
    ["version"]=>
    string(5) "1.2.1"
    ["time"]=>
    int(1444925238)
}

string(0) ""
```
## 更多调用
当需要重复调用时，需要先进行 **重置操作** ，如：
```php
//one more time
$rs = $client->reset()
    ->withService("User.GetBaseInfo")
    ->withParams("user_id", "1")
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());
```
  
当请求有异常时，返回的 ret!= 200，如：
```php
//illegal request
$rs = $client->reset()
    ->withService("XXX.XXXXX")
    ->withParams("user_id", "1")
    ->request();

var_dump($rs->getRet());
echo "\n";
var_dump($rs->getData());
echo "\n";
var_dump($rs->getMsg());
```
  
以上的输出为： 
```php
--------------------
int(200)

array(3) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(0) ""
  ["info"]=>
  array(3) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(7) "dogstar"
    ["from"]=>
    string(7) "oschina"
  }
}

string(0) ""

--------------------
int(400)

array(0) {
}

string(45) "非法请求：接口服务XXX.XXXXX不存在"

```
## 扩展你的过滤器和结果解析器
### (1)扩展过滤器
当服务端接口需要接口签名验证，或者接口参数加密传送，或者压缩传送时，可以实现此过滤器，以便和服务端操持一致。  
 
当需要扩展时，分两步。首先，需要实现过滤器接口：  
```php
<?php

class MyFilter implements PhalApiClientFilter {

        public function filter($service, array &$params) {
            //TODO ...
        }
}
```
然后设置过滤器：
```php
<?php

$rs = PhalApiClient.create()
	   .withHost("http://demo.phalapi.net/")
	   .withFilter(new MyFilter())
	   // ...
	   .request();
```
### (2)扩展结果解析器
当返回的接口结果不是JSON格式时，可以重新实现此接口。  
 
当需要扩展时，同样分两步。类似过滤器扩展，这里不再赘述。
