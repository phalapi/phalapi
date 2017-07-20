# XSHttpTool
##为PhalApi定制 基本架构遵循AFNetworking 

* 将所有网络请求管理放到一起方便管理调度,也能最大限度的简化调用
* 默认所有网络请求结果返回为Json,若需要自定义重载 **XSHttpTool.h** 文件中的+ (AFHTTPRequestOperationManager *)sharedManager方法

* 先在 **XSHttpTool.h** 文件中设置主体Api  
	
例如你要访问
```javascript
http://www.xsdota.com/PhalApi/Public/demo/?service=User.AppList
```
	
你应该这样设置:
```javascript
static NSString *const HOST_ADDRESS = @"http://www.xsdota.com/PhalApi/Public/demo";
static NSString *const EXTRA_STR    = @"?service=";
```
根据具体的需求可以灵活修改,这两个字符串就是拼接作用.

##GET请求
### 然后使用GET请求的时候这样使用:
```javascript
NSString *URLStr = @"User.AppList";
[XSHttpTool GET:URLStr param:nil success:^(id responseObject) {
        NSLog(@"%@",responseObject);
    } failure:^(NSError *error) {
        NSLog(@"%@",error);
    }];
```   

###  下面这个调用和上面结果一致   
 对于不同主机的api,直接请求URL也是可以的,不过要以http或https开头才能生效
```javascript
NSString *URLStr = @"http://www.xsdota.com/PhalApi/Public/demo/?service=User.AppList";
[XSHttpTool GET:URLStr param:nil success:^(id responseObject) {
        NSLog(@"%@",responseObject);
    } failure:^(NSError *error) {
        NSLog(@"%@",error);
    }];   
```    
    
### 带缓存的GET方法:GETCache  
   默认使用内存缓存,若要支持硬盘缓存需要在AppDelegate中的
```javascript
   - (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions方法中添加:
   NSURLCache *URLCache = [[NSURLCache alloc] initWithMemoryCapacity:4 * 1024 * 1024
                                                         diskCapacity:20 * 1024 * 1024
                                                             diskPath:nil];
    [NSURLCache setSharedURLCache:URLCache];
```
    
   缓存默认是以sqlite文件为载体存在于app的cache目录下,若要对缓存进行更进一步的操作,建议带上自己的diskPath,便于管理.
    
   
##POST请求
###普通的POST请求
```javascript
	NSDictionary *params = @{@"userName" : @"xiaos",
                             @"password" : @"111111" };
                             
    [XSHttpTool POST:@"User.TimeLines" param:params success:^(id responseObject) {
        NSLog(@"%@",responseObject);
    } failure:^(NSError *error) {
        NSLog(@"%@",error);
    }];
```    
###在body中存放数据的POST请求(用于上传一段二进制数据,比如一段文本或者一张图片)
```javascript	
    NSData *strData = [@"hello" dataUsingEncoding:NSUTF8StringEncoding];
    
    [XSHttpTool UpLoadData:strData to:@"User.TimeLines" withParamName:nil fileName:@"file" mimeType:@"text/html" param:nil success:^(id responseObject) {
        NSLog(@"%@",responseObject);
    } failure:^(NSError *error) {
        NSLog(@"%@",error);
    } uploadProgress:^(float uploadPercent) {
        NSLog(@"%f",uploadPercent);
    }];
```

###上传多张图片的POST请求
```javascript   
    NSArray *images = @[[UIImage imageNamed:@"0"],
                        [UIImage imageNamed:@"1"],
                        [UIImage imageNamed:@"2"]
                        ];
    [XSHttpTool upLoadImages:images to:@"User.UpImages" withParamName:nil ratio:0.1f param:nil success:^(id responseObject) {
        NSLog(@"%@",responseObject);
    } failure:^(NSError *error) {
        NSLog(@"%@",error);
    } uploadProgress:^(float uploadPercent) {
        NSLog(@"%f",uploadPercent);
    }];
```


