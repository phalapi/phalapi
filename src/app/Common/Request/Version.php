<?php
namespace App\Common\Request;

use PhalApi\Exception\BadRequestException;

class Version {

    public static function formatVersion($value, $rule) {
        if (count(explode('.', $value)) < 3) {
            throw new BadRequestException('版本号格式错误');
        }
        return $value;
    }
}
