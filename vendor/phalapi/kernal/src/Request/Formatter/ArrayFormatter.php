<?php
namespace PhalApi\Request\Formatter;

use PhalApi\Request\Formatter;
use PhalApi\Request\Formatter\BaseFormatter;
use PhalApi\Exception\BadRequestException;
use PhalApi\Exception\InternalServerErrorException;

/**
 * Formatter_Array 格式化数组
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */


class ArrayFormatter extends BaseFormatter implements Formatter {

    /**
     * 对数组格式化/数组转换
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'array', 'default' => '', 'format' => 'json/explode', 'separator' => '', 'min' => '', 'max' => '')
     * @return array
     */
    public function parse($value, $rule) {
        $rs = $value;

        if (!is_array($rs)) {
            $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
            if ($ruleFormat == 'explode') {
                // @dogstar 20221113 避免服务端配置了空的分割符
                $separator = isset($rule['separator']) ? $rule['separator'] : ',';
                if ($separator === '' || $separator === false) {
                    throw new InternalServerErrorException('separator CAT NOT be empty');
                }

                // @dogstar 20191020 当传递参数为空字符串时，解析为空数组array()，而不是包含一个空字符串的数组array('')
                $rs = $rs !== '' ? explode($separator, $rs) : array();
            } else if ($ruleFormat == 'json') {
                $rs = json_decode($rs, TRUE);

                if ((!empty($value) && $rs === NULL) || !is_array($rs)) {
                    $message = isset($rule['message']) 
                        ? \PhalApi\T($rule['message']) 
                        : \PhalApi\T('{name} illegal json data', array('name' => $rule['name']));
                    throw new BadRequestException($message);
                }
            } else {
                $rs = array($rs);
            }
        }

        $this->filterByRange(count($rs), $rule);

        return $rs;
    }
}
