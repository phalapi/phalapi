
## 推荐使用Gem
推荐使用：[PhalApi SDK for Ruby](https://github.com/phalapi/phalapi-sdk-ruby) 。  
  

## 使用说明
将框架目录下的 ./SDK/Ruby/PhalApiClient 目录中的全部代码拷贝到项目里面即可使用。  
 
## 代码示例
如下是使用的代码场景片段。  
 
首先，我们需要导入SDK包：
```ruby
#demo.rb

require_relative './PhalApiClient/phalapi_client'

a_client = PhalApi::Client.create.withHost('http://demo.phalapi.net')
a_response = a_client.withService('Default.Index').withParams('username', 'dogstar').withTimeout(3000).request()

puts a_response.ret, a_response.data, a_response.msg
```
 
## 运行效果
运行后，可以看到：  
```
200
{"title"=>"Hello World!", "content"=>"dogstar您好，欢迎使用PhalApi！", "version"=>"1.2.1", "time"=>1445741092}

```
## 6.5.4 更多调用
当需要重复调用时，需要先进行 **重置操作** ，如：
```ruby
#one more time
a_response = a_client.reset \
    .withService("User.GetBaseInfo") \
    .withParams("user_id", "1") \
    .request

puts a_response.ret, a_response.data, a_response.msg
```
  
当请求有异常时，返回的 ret!= 200，如：
```ruby
#illegal request
a_response = a_client.reset.withService('XXXX.noThisMethod').request

puts a_response.ret, a_response.data, a_response.msg
```
  
以上的输出为： 
```
--------------------
400
非法请求：接口服务XXXX.noThisMethod不存在

```
## 扩展你的过滤器和结果解析器
### (1)扩展过滤器
当服务端接口需要接口签名验证，或者接口参数加密传送，或者压缩传送时，可以实现此过滤器，以便和服务端操持一致。  
 
当需要扩展时，分两步。首先，需要实现过滤器接口：  
```ruby
class MyFilter < PhalApi::ClientFilter 
        def filter(service, *params)
            #TODO ...
        end
}
```
然后设置过滤器：
```ruby
a_response = PhalApi::Client.create.withHost('http://demo.phalapi.net') \
	   .withFilter(MyFilter.new) \
	   # ... \
	   .request
```
### (2)扩展结果解析器
当返回的接口结果不是JSON格式时，可以重新实现此接口。  
 
当需要扩展时，同样分两步。类似过滤器扩展，这里不再赘述。
  
## 一如既往的单元测试 
最后，附一张单元测试运行的效果图：  
![](http://7xiz2f.com1.z0.glb.clouddn.com/QQ20151025123152.png) 
