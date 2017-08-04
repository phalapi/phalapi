//
//  XSHttpTool.m
//
//  Created by xiaos on 14/11/10.
//  Copyright © 2015年 com.xsdota. All rights reserved.
//

#import "XSHttpTool.h"
//#import "MBProgressHUD+Extend.h"

@implementation XSHttpTool

#pragma mark - GET
+ (void)GET:(NSString *)requestKey
      param:(NSDictionary *)param
    success:(successBlock)success
    failure:(failureBlock)failure {

    [self checkReachability];
    
    NSString *URLStr = [self getURLStrByRequsetKey:requestKey];
    
    [[XSHttpTool sharedManager] GET:URLStr parameters:param success:^(AFHTTPRequestOperation *operation, id responseObject) {
        NSLog(@"%@",operation.response.URL);
        success(responseObject);
    } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
        failure(error);
    }];

}

+ (void)GETCache:(NSString *)requestKey
           param:(NSDictionary *)param
         success:(successBlock)success
         failure:(failureBlock)failure {
    
    [self checkReachability];
    
    NSString *URLStr = [self getURLStrByRequsetKey:requestKey];
    
    AFHTTPRequestOperationManager *mgr = [XSHttpTool sharedManager];
    mgr.requestSerializer.cachePolicy = NSURLRequestReturnCacheDataElseLoad;
    [mgr GET:URLStr parameters:param success:^(AFHTTPRequestOperation *operation, id responseObject) {
        NSLog(@"%@",operation.response.URL);
        success(responseObject);
    } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
        failure(error);
    }];
    
}

#pragma mark - POST
+ (void)POST:(NSString *)requestKey
       param:(NSDictionary *)param
     success:(successBlock)success
     failure:(failureBlock)failure
{
    [self checkReachability];
    
    NSString *URLStr = [self getURLStrByRequsetKey:requestKey];
    
    [[XSHttpTool sharedManager] POST:URLStr
       parameters:param
          success:^(AFHTTPRequestOperation *operation, id responseObject) {
            NSLog(@"%@",operation.response.URL);
            success(responseObject);
    } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
            failure(error);
    }];
    
}

+ (void)UpLoadData:(NSData *)data
                to:(NSString *)requestKey
     withParamName:(NSString *)paramName
          fileName:(NSString *)fileName
          mimeType:(NSString *)type
             param:(NSDictionary *)param
           success:(successBlock)success
           failure:(failureBlock)failure
    uploadProgress:(progressBlock)uploadProgress {
    
    [self checkReachability];
    
    NSString *URLStr = [self getURLStrByRequsetKey:requestKey];
    
    AFHTTPRequestOperation *operation =
    [[XSHttpTool sharedManager]
     POST:URLStr
     parameters:param
     constructingBodyWithBlock:^(id<AFMultipartFormData> formData) {
         [formData appendPartWithFileData:data name:paramName fileName:fileName mimeType:type];
     } success:^(AFHTTPRequestOperation *operation, id responseObject) {
         success(responseObject);
     } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
         failure(error);
     }];
    
    [operation setUploadProgressBlock:^(NSUInteger bytesWritten, long long totalBytesWritten, long long totalBytesExpectedToWrite) {
        CGFloat percent = totalBytesWritten * 1.0 / totalBytesExpectedToWrite;
        uploadProgress(percent);
    }];
}

+ (void)upLoadImages:(NSArray *)images
                  to:(NSString *)requestKey
       withParamName:(NSString *)paramName
               ratio:(float)ratio
               param:(NSDictionary *)param
             success:(successBlock)success
             failure:(failureBlock)failure
      uploadProgress:(progressBlock)uploadProgress {
    [self checkReachability];
    
    NSString *URLStr = [self getURLStrByRequsetKey:requestKey];
    
    AFHTTPRequestOperation *operation =
    [[XSHttpTool sharedManager]
     POST:URLStr
     parameters:param
     constructingBodyWithBlock:^(id<AFMultipartFormData> formData) {
         NSUInteger i = 0;
         NSDateFormatter *formatter = [[NSDateFormatter alloc]init];
         [formatter setDateFormat:@"yyyyMMddHHmm"];
         NSString *dateString = [formatter stringFromDate:[NSDate date]];
         
         for (UIImage *image in images) {
             NSString *fileName = [NSString stringWithFormat:@"%@_%lu.png",dateString,(unsigned long)i];
             NSData *imageData;
             if (ratio > 0.0f && ratio < 1.0f) {
                 imageData = UIImageJPEGRepresentation(image, ratio);
             }else{
                 imageData = UIImageJPEGRepresentation(image, 1.0f);
             }
             
             [formData appendPartWithFileData:imageData name:paramName fileName:fileName mimeType:@"image/jpg/png/jpeg"];
         }
         
     } success:^(AFHTTPRequestOperation *operation, id responseObject) {
         success(responseObject);
     } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
         failure(error);
     }];
    
    [operation setUploadProgressBlock:^(NSUInteger bytesWritten, long long totalBytesWritten, long long totalBytesExpectedToWrite) {
        CGFloat percent = totalBytesWritten * 1.0 / totalBytesExpectedToWrite;
        uploadProgress(percent);
    }];
}

#pragma mark - 设置单例 超时请求响应解析
+ (AFHTTPRequestOperationManager *)sharedManager {
    AFHTTPRequestOperationManager *manager = [AFHTTPRequestOperationManager manager];
    [manager.requestSerializer setTimeoutInterval:TIMEOUT];
//    manager.
    //header 设置
    //    [manager.requestSerializer setValue:K_PASS_IP forHTTPHeaderField:@"Host"];
    //    [manager.requestSerializer setValue:@"max-age=0" forHTTPHeaderField:@"Cache-Control"];
    //    [manager.requestSerializer setValue:@"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" forHTTPHeaderField:@"Accept"];
    //    [manager.requestSerializer setValue:@"zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3" forHTTPHeaderField:@"Accept-Language"];
    //    [manager.requestSerializer setValue:@"gzip, deflate" forHTTPHeaderField:@"Accept-Encoding"];
    //    [manager.requestSerializer setValue:@"keep-alive" forHTTPHeaderField:@"Connection"];
    //    [manager.requestSerializer setValue:@"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:35.0) Gecko/20100101 Firefox/35.0" forHTTPHeaderField:@"User-Agent"];
    manager.requestSerializer.cachePolicy = 0;
    manager.responseSerializer.acceptableContentTypes = [NSSet setWithObjects:@"text/plain", @"text/html", @"application/json", nil];
    return manager;
}

#pragma mark - 检测网络连接状态
+ (void)checkReachability
{
    [[AFNetworkReachabilityManager sharedManager] startMonitoring];
    // 连接状态回调处理
    [[AFNetworkReachabilityManager sharedManager] setReachabilityStatusChangeBlock:^(AFNetworkReachabilityStatus status) {
         switch (status) {
             case AFNetworkReachabilityStatusUnknown:
                 NSLog(@"Unkonw Collection");
                 break;
             case AFNetworkReachabilityStatusNotReachable:
                 NSLog(@"网络未连接");
//                 [MBProgressHUD showError:@"网络未连接"];
                 break;
             case AFNetworkReachabilityStatusReachableViaWWAN:
                 NSLog(@"WWAN Collection");
                 break;
             case AFNetworkReachabilityStatusReachableViaWiFi:
                 NSLog(@"WiFi Collection");
                 break;
         }
     }];
}

#pragma mark -  拼接出完整的URL
+ (NSString *)getURLStrByRequsetKey:(NSString *)requestKey
{
    /** 可以传入完整URL地址 会自动覆盖主API */
    if ([requestKey hasPrefix:@"http://"] || [requestKey hasPrefix:@"https://"]) {
        return [requestKey stringByRemovingPercentEncoding];
    }
    
    NSString *URLStr = [[HOST_ADDRESS stringByAppendingPathComponent:[NSString stringWithFormat:@"%@%@",EXTRA_STR,requestKey]] stringByRemovingPercentEncoding];
    return URLStr;
}

#pragma mark - 自定义cache管理
+ (NSString *)cachesDirectory {
    NSString * cachesDirectory = NSSearchPathForDirectoriesInDomains(NSCachesDirectory, NSUserDomainMask, YES).firstObject;
    
    return cachesDirectory;
}

+ (NSString *)cachePathWithIdentifier:(NSString *)cacheIdentifier {
    NSString * cachePath = [self cachesDirectory];
    cachePath = [cachePath stringByAppendingPathComponent:cacheIdentifier];
    
    BOOL isDirectory = NO;
    BOOL fileExists = [[NSFileManager defaultManager] fileExistsAtPath:cachePath isDirectory:&isDirectory];
    
    if (fileExists == NO || isDirectory) {
        BOOL createFileSuccess = [[NSFileManager defaultManager] createFileAtPath:cachePath contents:nil attributes:nil];
        if (createFileSuccess == NO) {
            NSLog(@"创建缓存文件失败"); //
            return nil;
        }
        else {
            NSLog(@"创建缓存文件成功"); //
        }
    }
    else {
        NSLog(@"缓存文件之前已存在"); //
    }
    
    return cachePath;
}

+ (BOOL)saveCache:(NSDictionary *)cache
   withIdentifier:(NSString *)cacheIdentifier {
    NSCAssert([cache isKindOfClass:[NSDictionary class]], @"缓存对象不是字典类型");
    
    NSString * cachePath = [self cachePathWithIdentifier:cacheIdentifier];
    
    BOOL saveSuccess = [cache writeToFile:cachePath atomically:YES];
    if (saveSuccess) {
        NSLog(@"储存缓存记录成功");
        return saveSuccess;
    }
    else {
        NSLog(@"储存缓存记录失败"); 
        return saveSuccess;
    }
}

+ (NSDictionary *)cacheWithIdentifier:(NSString *)cacheIdentifier {
    NSString * cachePath = [XSHttpTool cachePathWithIdentifier:cacheIdentifier];
    NSDictionary * cache = [NSDictionary dictionaryWithContentsOfFile:cachePath];
    if (cache) {
        NSLog(@"并不存在缓存");
    }
    NSError * error = nil;
    NSDictionary * cacheFileAttributes = [[NSFileManager defaultManager]attributesOfItemAtPath:cachePath error:&error];
    if (error) {
        
        NSLog(@"缓存已过期");
    }
    else if ([[NSDate date]timeIntervalSince1970] - [cacheFileAttributes.fileModificationDate timeIntervalSince1970] > JSON_CACHE_DURATION) {
        
        NSLog(@"缓存已过期");
        
        BOOL removeCacheSuccess = [[NSFileManager defaultManager] removeItemAtPath:cachePath error:&error];
        if (removeCacheSuccess) {
            NSLog(@"移除已过期的缓存成功");
        }
        else {
            NSLog(@"移除已过期的缓存失败");
        }
        return nil;
    }
    else {
        
    }
    
    return cache;
}


@end
