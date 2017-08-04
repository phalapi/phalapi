//
//  PhalApiClient.h
//  PhalApiClientDemo
//
//  Created by Aevit on 15/10/17.
//  Copyright © 2015年 Aevit. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "PhalApiClientFilter.h"

#if 1
#   define PALog(x, ...) NSLog(x, ## __VA_ARGS__);
#else
#   define PALog(x, ...)
#endif

typedef void(^HttpCompleteBlock)(id resultObject);
typedef void(^HttpFailureBlock)(id error);
typedef void(^FormDataBlock)(id formData);

@interface PhalApiClient : NSObject


@property (nonatomic, copy) NSString *host;
@property (nonatomic, copy) NSString *service;
@property (nonatomic, strong) NSMutableDictionary *params;
@property (nonatomic, assign) float timeout;

@property (nonatomic, strong) PhalApiClientFilter *filter;


#pragma mark - generate a client
/**
 *  生成单例
 *
 *  @return 单例PhalApiClient
 */
+ (PhalApiClient*)sharedClient;

/**
 *  生成非单例
 *
 *  @return 非单例PhalApiClient
 */
+ (PhalApiClient*)create;

#pragma mark - configure url and params
/**
 *  重复查询时须重置请求状态，包括接口服务名称、接口参数和超时时间
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)reset;

/**
 *  设置接口域名
 *  如果整个客户端只有一个接口host，可以在"commonInit"方法设置好host，这样外部就可以不用调用withHost方法了
 *
 *  @param host 域名
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withHost:(NSString*)host;

/**
 *  设置将在调用的接口服务名称，如：Default.Index
 *
 *  @param service 接口服务名称
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withService:(NSString*)service;

/**
 *  设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
 *
 *  @param params 参数
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withParams:(NSDictionary*)params;

/**
 *  设置过滤器，与服务器的DI().filter对应
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withFilter:(PhalApiClientFilter*)filter;

/**
 *  设置超时时间，单位毫秒
 *
 *  @param timeout 超时时间，单位秒
 *
 *  @return PhalApiClient实例
 */
- (PhalApiClient*)withTimeout:(float)timeout;

#pragma mark - request
/**
 *  发起接口请求（POST请求）
 *
 *  @param completeBlock 请求成功的回调
 *  @param failureBlock  请求失败的回调
 *
 *  @return 请求实例
 */
- (id)request:(HttpCompleteBlock)completeBlock failureBlock:(HttpFailureBlock)failureBlock;

/**
 *  发起接口请求(提交表单)
 *
 *  @param formDataBlock 表单内容
 *  @param completeBlock 请求成功的回调
 *  @param failureBlock  请求失败的回调
 *
 *  @return 请求实例
 */
- (id)requestWithFormDataBlock:(FormDataBlock)formDataBlock completeBlock:(HttpCompleteBlock)completeBlock failureBlock:(HttpFailureBlock)failureBlock;


#pragma mark - utils
/**
 *  以get形式打印整个url，方便服务端开发人员调试
 *
 *  @return 整个url
 */
- (NSString*)printTotalUrlStr;

@end
