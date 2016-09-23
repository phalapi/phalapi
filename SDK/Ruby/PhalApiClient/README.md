# PhalApi SDK for Ruby

PhalApi开源接口框架SDK包，更多请访问：[http://www.phalapi.net/](http://www.phalapi.net/)  
  
支持的Ruby版本：
+ Ruby 2.3.x
  
## 安装

在您 Ruby 应用程序的```Gemfile```文件中，添加如下一行代码：

```ruby
gem 'phalapi', '>= 1.3.4'
```

然后，在应用程序所在的目录下，可以运行```bundle```安装依赖包：
```
$ bundle
```

或者，可以使用 Ruby 的包管理器```gem```进行安装：
```
$ gem install phalapi
```

## 使用

```
require 'phalapi'

a_client = PhalApi::Client.create.withHost('http://demo.phalapi.net')
a_response = a_client.withService('Default.Index').withParams('username', 'dogstar').withTimeout(3000).request()

puts a_response.ret, a_response.data, a_response.msg

# => 200
# => {"title"=>"Hello World!", "content"=>"dogstar您好，欢迎使用PhalApi！", "version"=>"1.2.1", "time"=>1445741092}
# =>
```
  
更多请参考：[SDK包（Ruby版）](http://www.phalapi.net/wikis/%5B6.6%5D-SDK%E5%8C%85%EF%BC%88Ruby%E7%89%88%EF%BC%89.html)

## 贡献代码

+ 1. Fork
+ 2. 创建您的特性分支
+ 3. 提交您的改动
+ 4. 将您的修改记录提交到远程 git 仓库
+ 5. 发起 Pull Request

## 许可证
Copyright (c) 2015-2016 PhalApi All Rights Reserved.   
  
基于 MIT 协议发布: [MIT License](http://opensource.org/licenses/MIT)。

