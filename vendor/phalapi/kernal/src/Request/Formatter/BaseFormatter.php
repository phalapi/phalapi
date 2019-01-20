<?php
namespace PhalApi\Request\Formatter;

use PhalApi\Request\Formatter;
use PhalApi\Exception\BadRequestException;
use PhalApi\Exception\InternalServerErrorException;

/**
 * BaseFormatter 公共基类
 *
 * - 提供基本的公共功能，便于子类重用
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */


class BaseFormatter {

    /**
     * 根据范围进行控制
     */
    protected function filterByRange($value, $rule) {
        $this->filterRangeMinLessThanOrEqualsMax($rule);

        $this->filterRangeCheckMin($value, $rule);

        $this->filterRangeCheckMax($value, $rule);

        return $value;
    }

    protected function filterRangeMinLessThanOrEqualsMax($rule) {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            throw new InternalServerErrorException(
                \PhalApi\T('min should <= max, but now {name} min = {min} and max = {max}',
                    array('name' => $rule['name'], 'min' => $rule['min'], 'max' => $rule['max']))
            );
        }
    }

    protected function filterRangeCheckMin($value, $rule) {
        if (isset($rule['min']) && $value < $rule['min']) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('{name} should >= {min}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'min' => $rule['min'], 'value' => $value));
            throw new BadRequestException($message);
        }
    }

    protected function filterRangeCheckMax($value, $rule) {
        if (isset($rule['max']) && $value > $rule['max']) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('{name} should <= {max}, but now {name} = {value}',
                    array('name' => $rule['name'], 'max' => $rule['max'], 'value' => $value));
            throw new BadRequestException($message);
        }
    }

    /**
     * 格式化枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws BadRequestException
     */
    protected function formatEnumValue($value, $rule) {
        if (!in_array($value, $rule['range'])) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('{name} should be in {range}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'range' => implode('/', $rule['range']), 'value' => $value));
            throw new BadRequestException($message);
        }
    }
}
