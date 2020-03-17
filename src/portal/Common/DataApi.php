<?php
namespace Portal\Common;

use PhalApi\Api\DataApi as PhalApiDataApi;

/**
 * 通用数据接口
 * @author dogstra 20200309
 */
class DataApi extends PhalApiDataApi {
    
    /**
     * 管理员身份检测
     */
    protected function userCheck() {
        \PhalApi\DI()->admin->check();
    }
    
}
