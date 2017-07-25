
```
该文档由 @Aevit 提供
```
## 使用说明
将框架目录下的 ./SDK/Objective-C/ 目录中的全部代码拷贝到项目里面即可使用。如：  
![](http://aevit.qiniudn.com/PhalApiClient-SDK-files.jpeg)  

```
请求方式，可以使用系统的NSURLSession，或第三方的网络请求库等
这里我们使用第三方的AFNetworking，进行二次封装（https://github.com/AFNetworking/AFNetworking）
如需使用其他网络请求方式（如官方的NSURLSession），请继承自PhalApiClient，重写request方法即可，可参考AFNPhalApiClient
```  

## 代码示例
如下是使用的代码场景片段。  
  
首先，我们需要导入SDK包：
```Objective-C
#import "AFNPhalApiClient.h"
```
  
然后，准备按以下方法实现接口请求即可：  
```Objective-C
// 待POST的参数
NSDictionary *params = @{@"demo_key_1": @"your_key", @"demo_key_2": @"1.0"};

// 使用AFNPhalApiClient
[[[[[AFNPhalApiClient sharedClient] withHost:@"http://api1.aevit.xyz/"] withService:@"Default.Index"] withParams:params] requestWithFormDataBlock:^(id formData) {
	// 如需上传文件（图片等），请参照如下格式
    [formData appendPartWithFileData:UIImageJPEGRepresentation([UIImage imageNamed:@"head.JPG"], 1) name:@"file" fileName:@"image.jpg" mimeType:@"image/jpeg"];
} completeBlock:^(id resultObject) {
    PALog(@"resultObject: %@", resultObject);
} failureBlock:^(NSError *error) {
    PALog(@"error: %@", error);
}];

// 打印url查看
PALog(@"total url: %@", [[AFNPhalApiClient sharedClient] printTotalUrlStr]);
```


## 运行效果
运行后，查询log，可以看到：  
![](http://aevit.qiniudn.com/PhalApiClient-SDK-demo-result.jpg)  

  
可以注意到，调用完成后，会有接口请求的链接和返回的结果，如：  
```
2015-10-19 18:28:01.503 PhalApiClientDemo[23161:1199740] total url: http://api1.aevit.xyz/?service=Default.Index&amp;demo_key_2=1.0&amp;demo_key_1=your_key

2015-10-19 18:28:01.536 PhalApiClientDemo[23161:1199740] resultObject: {
    data =     {
        content = "PHPer\U60a8\U597d\Uff0c\U6b22\U8fce\U4f7f\U7528PhalApi\Uff01";
        time = 1445250481;
        title = "Hello World!";
        version = "1.2.1";
    };
    msg = "";
    ret = 200;
}
```

## 扩展你的过滤器和结果解析器
### (1)扩展过滤器
当服务端接口需要接口签名验证，或者接口参数加密传送，或者压缩传送时，可以实现此过滤器，以便和服务端操持一致。  
  
当需要扩展时，分两步。首先，需要实现过滤器接口：  
```Objective-C

@interface MyFilter : PhalApiClientFilter
@end

@implementation PhalApiClientFilter
/**
 *  接口过滤器
 *  可用于接口签名生成
 *
 *  @param service 接口服务名称
 *  @param params  接口参数，注意是mutable变量，可以直接修改
 */
- (void)filter:(NSString*)service params:(NSMutableDictionary*)params {
    // 在此对接口进行过滤
}
@end
```
然后设置过滤器：
```Objective-C
[[[[[[AFNPhalApiClient sharedClient] withHost:@"http://api1.aevit.xyz/"] withService:@"Default.Index"] withParams:params]
      withFilter:[MyFilter new]] // filter
     requestWithFormDataBlock:^(id formData) {
    } completeBlock:^(id resultObject) {
    } failureBlock:^(NSError *error) {
}];
```
### (2)扩展结果解析器
当返回的接口结果不是JSON格式时，如XML，请上google搜索“AFNetworking XML”相关资料即可。  
