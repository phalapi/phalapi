<?php
namespace PhalApi\Request\Formatter;

use PhalApi\Request\Formatter;
use PhalApi\Request\Formatter\BaseFormatter;

/**
 * DateFormatter 格式化日期
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */

class DateFormatter extends BaseFormatter implements Formatter {

    /**
     * 对日期进行格式化
     *
     * @param timestamp $value 变量值
     * @param array $rule array('format' => 'timestamp', 'min' => '最小值', 'max' => '最大值')
     * @return timesatmp/string 格式化后的变量
     *
     */
    public function parse($value, $rule) {
        $rs = $value;

        $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($ruleFormat == 'timestamp') {
            $rs = strtotime($value);
            if ($rs <= 0) {
            	$rs = 0;
            }

            if (isset($rule['min']) && !is_numeric($rule['min'])) {
                $rule['min'] = strtotime($rule['min']);
            }
            if (isset($rule['max']) && !is_numeric($rule['max'])) {
                $rule['max'] = strtotime($rule['max']);
            }

            $rs = $this->filterByRange($rs, $rule);
        }

        return $rs;
    }
}
