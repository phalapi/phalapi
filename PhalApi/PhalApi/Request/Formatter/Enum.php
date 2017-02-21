<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * PhalApi_Request_Formatter_Enum 格式化枚举类型
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */

class PhalApi_Request_Formatter_Enum extends PhalApi_Request_Formatter_Base implements PhalApi_Request_Formatter {

    /**
     * 检测枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @return 当不符合时返回$rule
     */
    public function parse($value, $rule) {
        $this->formatEnumRule($rule);

        $this->formatEnumValue($value, $rule);

        return $value;
    }

    /**
     * 检测枚举规则的合法性
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws PhalApi_Exception_InternalServerError
     */
    protected function formatEnumRule($rule) {
        if (!isset($rule['range'])) {
            throw new PhalApi_Exception_InternalServerError(
                T("miss {name}'s enum range", array('name' => $rule['name'])));
        }

        if (empty($rule['range']) || !is_array($rule['range'])) {
            throw new PhalApi_Exception_InternalServerError(
                T("{name}'s enum range can not be empty", array('name' => $rule['name'])));
        }
    }
}
