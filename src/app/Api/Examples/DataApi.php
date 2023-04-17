<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 接口示例
 */
class DataApi extends \PhalApi\Api\DataApi {
    protected function userCheck() {
        // TODO 记得要进行验证
    }

    protected function getDataModel() {
        throw new \PhalApi\Exception\BadRequestException('示例未指定Model子类');
        // return new \App\Model\CURD();
    }
}

