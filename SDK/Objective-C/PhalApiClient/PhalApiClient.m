//
//  PhalApiClient.m
//  PhalApiClientDemo
//
//  Created by Aevit on 15/10/17.
//  Copyright © 2015年 Aevit. All rights reserved.
//

#import "PhalApiClient.h"

@interface PhalApiClient()

@end

@implementation PhalApiClient

- (instancetype)init {
    self = [super init];
    if (self) {
        [self commonInit];
    }
    return self;
}

- (void)commonInit {
    [self reset];
    /**
     *  如果整个客户端只有一个接口host，可以在reset后设置好host，这样外部就可以不用调用withHost方法了
     *  e.g. [self withHost:@"http://api.your_host.com/project_name/"];
     */
}

#pragma mark - generate a client
/**
 *  生成单例
 *
 *  @return 单例PhalApiClient
 */
+ (PhalApiClient*)sharedClient {
    static PhalApiClient *_sharedClient;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        _sharedClient = [[PhalApiClient alloc] init];
    });
    return _sharedClient;
}

/**
 *  生成非单例
 *
 *  @return 非单例PhalApiClient
 */
+ (PhalApiClient*)create {
    PhalApiClient *client = [[PhalApiClient alloc] init];
    return client;
}

#pragma mark - configure url and params
/**
 *  重复查询时须重置请求状态，包括接口服务名称、接口参数和超时时间
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)reset {
    self.host = nil;
    self.service = nil;
    self.params = nil;
    self.timeout = 0;
    return self;
}

/**
 *  设置接口域名
 *  如果整个客户端只有一个接口host，可以在"commonInit"方法设置好host，这样外部就可以不用调用withHost方法了
 *
 *  @param host 域名
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withHost:(NSString*)host {
    self.host = nil;
    self.host = host;
    return self;
}

- (NSString *)host {
    if ([[_host substringFromIndex:(_host.length - 1)] isEqualToString:@"/"]) {
        return _host;
    }
    _host = [NSString stringWithFormat:@"%@/", _host];
    return _host;
}

/**
 *  设置将在调用的接口服务名称，如：Default.Index
 *
 *  @param service 接口服务名称
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withService:(NSString*)service {
    self.service = nil;
    self.service = service;
    return self;
}

/**
 *  设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
 *
 *  @param params 参数
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withParams:(NSDictionary*)params {
    self.params = nil;
    if (!params) {
        return self;
    }
    self.params = [NSMutableDictionary dictionaryWithDictionary:params];
    return self;
}

/**
 *  设置过滤器，与服务器的DI().filter对应
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withFilter:(PhalApiClientFilter*)filter {
    self.filter = filter;
    return self;
}

/**
 *  设置超时时间，单位毫秒
 *
 *  @param timeout 超时时间，单位秒
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withTimeout:(float)timeout {
    self.timeout = timeout;
    return self;
}

- (float)timeout {
    // 默认60s超时
    return (_timeout > 0 ? _timeout : 60);
}

#pragma mark - request
/**
 *  发起接口请求（POST请求）
 *
 *  @param completeBlock 请求成功的回调
 *  @param failureBlock  请求失败的回调
 *
 *  @return 请求实例
 */
- (id)request:(HttpCompleteBlock)completeBlock failureBlock:(HttpFailureBlock)failureBlock {
    /**
     *  请求方式，可以使用系统的NSURLSession，或第三方的网络请求库等
     *  这里我们使用第三方的AFNetworking，进行二次封装（https://github.com/AFNetworking/AFNetworking）
     *  新建子类（AFNPhalApiClient，继承自PhalApiClient），重写此request方法
     *  如需使用其他网络请求方式（如NSURLSession），请继承自PhalApiClient，重写此request即可，可参考AFNPhalApiClient
     */
    return [self requestWithFormDataBlock:nil completeBlock:completeBlock failureBlock:failureBlock];
}

/**
 *  发起接口请求(提交表单)
 *
 *  @param formDataBlock 表单内容
 *  @param completeBlock 请求成功的回调
 *  @param failureBlock  请求失败的回调
 *
 *  @return 请求实例
 */
- (id)requestWithFormDataBlock:(FormDataBlock)formDataBlock completeBlock:(HttpCompleteBlock)completeBlock failureBlock:(HttpFailureBlock)failureBlock {
    /**
     *  请求方式，可以使用系统的NSURLSession，第三方的网络请求库等
     *  这里我们使用第三方的AFNetworking，进行二次封装（https://github.com/AFNetworking/AFNetworking）
     *  新建子类（AFNPhalApiClient，继承自PhalApiClient），重写此request方法
     *  如需使用其他网络请求方式（如NSURLSession），请继承自PhalApiClient，重写此request即可，可参考AFNPhalApiClient
     */
    return nil;
}


#pragma mark - utils
/**
 *  以get形式打印整个url，方便服务端开发人员调试
 *
 *  @return 整个url
 */
- (NSString*)printTotalUrlStr {
    
    if (!_host || _host.length <= 0) {
        return @"empty host";
    }
    
    NSMutableString *finalStr = [NSMutableString stringWithString:_host];
    [finalStr appendFormat:@"?service=%@&", _service];
    
    if (_params) {
        for (NSString *key in [_params allKeys]) {
            [finalStr appendString:key];
            [finalStr appendString:@"="];
            id value = [_params objectForKey:key];
            NSString *valueStr = @"";
            if ([value isKindOfClass:[NSNumber class]]) {
                valueStr = [value stringValue];
            } else if ([value isKindOfClass:[NSString class]]) {
                valueStr = value;
            } else {
                PALog(@"什么鬼？！");
            }
            [finalStr appendString:valueStr];
            [finalStr appendString:@"&"];
        }
    }
    return [finalStr substringWithRange:NSMakeRange(0, finalStr.length - 1)];
}

@end


