//
//  XSHttpTool.h
//
//  Created by xiaos on 14/11/10.
//  Copyright © 2015年 com.xsdota. All rights reserved.
//
#import "AFNetworking.h"

/** 设置主体API */
static NSString *const HOST_ADDRESS = @"http://localhost:7888/Phalapi/Public/Demo";
/** 设置API后的特定字符 */
static NSString *const EXTRA_STR    = @"?service=";


/** 缓存保存时间 */
static NSTimeInterval const TIMEOUT = 30;
static NSTimeInterval const JSON_CACHE_DURATION = 3 * 24 * 60 * 60;
static NSTimeInterval const IMAGE_CACHE_DURATION = 3 * 24 * 60 * 60;

/** 成功失败进度的闭包 */
typedef void(^successBlock)(id responseObject);
typedef void(^failureBlock)(NSError *error);
typedef void(^progressBlock)(float uploadPercent);

@interface XSHttpTool : NSObject

+ (AFHTTPRequestOperationManager *)sharedManager;

#pragma mark - GET
#pragma mark  普通GET请求
+ (void)GET:(NSString *)requestKey
      param:(NSDictionary *)param
    success:(successBlock)success
    failure:(failureBlock)failure;

#pragma mark  带缓存的GET请求 用于不常更新的数据
+ (void)GETCache:(NSString *)requestKey
           param:(NSDictionary *)param
         success:(successBlock)success
         failure:(failureBlock)failure;

#pragma mark - POST
#pragma mark  普通POST请求
+ (void)POST:(NSString *)requestKey
       param:(NSDictionary *)param
     success:(successBlock)success
     failure:(failureBlock)failure;

#pragma mark 上传文件POST请求
+ (void)UpLoadData:(NSData *)data
                to:(NSString *)requestKey
     withParamName:(NSString *)paramName
          fileName:(NSString *)fileName
          mimeType:(NSString *)type
             param:(NSDictionary *)param
           success:(successBlock)success
           failure:(failureBlock)failure
    uploadProgress:(progressBlock)uploadProgress;

#pragma mark 上传多图的POST请求
/**
 *  上传带图片的内容，允许多张图片上传（URL）POST
 *
 *  @param url                 网络请求地址
 *  @param images              要上传的图片数组（注意数组内容需是图片）
 *  @param parameter           图片数组对应的参数  
 *  @param parameters          其他参数字典@
 *  @param ratio               图片的压缩比例（0.0~1.0之间）
 *  @param succeedBlock        成功的回调
 *  @param failedBlock         失败的回调
 *  @param uploadProgressBlock 上传进度的回调
 */
+ (void)upLoadImages:(NSArray *)images
                  to:(NSString *)requestKey
       withParamName:(NSString *)paramName
               ratio:(float)ratio
               param:(NSDictionary *)param
             success:(successBlock)success
             failure:(failureBlock)failure
      uploadProgress:(progressBlock)uploadProgress;


#pragma mark - cache管理
+ (BOOL)saveCache:(NSDictionary *)cache withIdentifier:(NSString *)cacheIdentifier;
+ (NSDictionary *)cacheWithIdentifier:(NSString *)cacheIdentifier;

@end
