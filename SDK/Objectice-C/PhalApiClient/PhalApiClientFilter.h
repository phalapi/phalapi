//
//  PhalApiClientFilter.h
//  PhalApiClientDemo
//
//  Created by Aevit on 15/10/18.
//  Copyright © 2015年 Aevit. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PhalApiClientFilter : NSObject

/**
 *  接口过滤器
 *  可用于接口签名生成
 *
 *  @param service 接口服务名称
 *  @param params  接口参数，注意是mutable变量，可以直接修改
 */
- (void)filter:(NSString*)service params:(NSMutableDictionary*)params;

@end
