//
//  AFNPhalApiClient.m
//  PhalApiClientDemo
//
//  Created by Aevit on 15/10/18.
//  Copyright © 2015年 Aevit. All rights reserved.
//

#import "AFNPhalApiClient.h"
#import "PhalApiClientFilter.h"

@implementation AFNPhalApiClient

#pragma mark - generate a client
/**
 *  生成单例
 *
 *  @return 单例PhalApiClient
 */
+ (AFNPhalApiClient*)sharedClient {
    static AFNPhalApiClient *_sharedClient;
    static dispatch_once_t onceToken;
    dispatch_once(&onceToken, ^{
        _sharedClient = [[AFNPhalApiClient alloc] init];
    });
    return _sharedClient;
}

/**
 *  生成非单例
 *
 *  @return 非单例PhalApiClient
 */
+ (AFNPhalApiClient*)create {
    AFNPhalApiClient *client = [[AFNPhalApiClient alloc] init];
    return client;
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
    
    if (self.filter) {
        // 过滤器，可生成签名验证等
        [self.filter filter:self.service params:self.params];
    }
    
    NSString *url = self.host;
    if (self.service && self.service.length > 0) {
        // 一般来说，建议service以GET方式写在url里，其他所有参数，统一用POST方式
        url = [NSString stringWithFormat:@"%@?service=%@", self.host, self.service];
    }
    
    AFHTTPRequestOperationManager *manager = [AFHTTPRequestOperationManager manager];
    manager.responseSerializer.acceptableContentTypes = [NSSet setWithObjects:@"text/html", @"text/plain", @"text/json", @"text/xml", nil];
    manager.requestSerializer.timeoutInterval = self.timeout;
    
    AFHTTPRequestOperation *operation = [manager POST:url parameters:self.params constructingBodyWithBlock:^(id<AFMultipartFormData>  _Nonnull formData) {
        if (formDataBlock) {
            formDataBlock(formData);
        }
    } success:^(AFHTTPRequestOperation * _Nonnull operation, id  _Nonnull responseObject) {
        // 默认以JSON返回，如需其他格式（如XML，请上google搜索"AFNetworking XML"相关资料即可）
        if (completeBlock) {
            completeBlock(responseObject);
        }
    } failure:^(AFHTTPRequestOperation * _Nonnull operation, NSError * _Nonnull error) {
        if (error.code == NSURLErrorTimedOut) {
            NSDictionary *timeoutDict = @{@"ret": @408, @"data": @{}, @"msg": @"Request Timeout"};
            failureBlock(timeoutDict);
            return ;
        }
        if (failureBlock) {
            failureBlock(error);
        }
    }];
    
    return operation;
}

@end
