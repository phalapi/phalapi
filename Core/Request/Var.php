<?php
/**
 * Core_Request_Var 变量格式化类
 *
 * - 针对设定的规则进行对品购模块中的变量进行格式化操作
 *  1、根据字段与预定义变量对应关系，获取变量值
 *  2、对变量进行类型转换
 *  3、进行有效性判断过滤
 *  4、按业务需求进行格式化 
 * 
 * - 格式规则：
 *  array('name' => '', 'type' => 'string', 'default' => '', 'min' => '', 'max' => '',)
 *  array('name' => '', 'type' => 'int', 'default' => '', 'min' => '', 'max' => '',)
 *  array('name' => '', 'type' => 'float', 'default' => '', 'min' => '', 'max' => '',)
 *  array('name' => '', 'type' => 'boolean', 'default' => '',)
 *  array('name' => '', 'type' => 'date', 'default' => '',)
 *  array('name' => '', 'type' => 'array', 'default' => '', 'format' => 'json/explode', 'separator' => '')
 *  array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
 *
 * @author: dogstar 2014-10-04
 */

class Core_Request_Var
{
    /** ------------------ 对外开放操作 ------------------ **/

    /**
     * 统一格式化操作
     *
     * @param string $varName 变量名
     * @param array $rule 格式规则：
     * array(
     *  'name' => '变量名', 
     *  'type' => '类型', 
     *  'default' => '默认值', 
     *  'format' => '格式化字符串'
     *  ...
     *  )
     * 扩展参数请参见各种类型格式化操作的参数说明
     * @param array $params 参数列表
     * @return miexd 格式后的变量
     *
     */ 
    public static function format($varName, $rule, $params)
    {
        $value = isset($rule['default']) ? $rule['default'] : null;
        $type = !empty($rule['type']) ? strtolower($rule['type']) : 'string';

        $key = isset($rule['name']) ? $rule['name'] : $varName;
        $value = isset($params[$key]) ? $params[$key] : $value;

        if ($value === null) {
            return $value;
        }

        switch ($type) {
            //基本类型
            case 'string':
                $value = self::formatString($value, $rule);
                break;
            case 'int':
                $value = self::formatInt($value, $rule);
                break;
            case 'float':
                $value = self::formatFloat($value, $rule);
                break;
            case 'boolean':
                $value = self::formatBoolean($value, $rule);
                break;
            //扩展常用类型
            case 'date':
                $value = self::formatDate($value, $rule);
                break;
            case 'array':
                $value = self::formatArray($value, $rule);
                break;
            //枚举类型
            case 'enum':
                $value = self::formatEnum($value, $rule);
                break;
            default:
                break;
        }

        return $value;
    }

    /** ------------------ 针对各种类型的格式化操作 ------------------ **/

    /**
     * 对字符串进行格式化
     *
     * @param mixed $value 变量值
     * @parma array $rule array('len' => ‘最长长度’)
     * @return string 格式化后的变量
     *
     */
    public static function formatString($value, $rule)
    {
        $rs = strval($value);

        $rs = strval(self::filterByStrLen($rs, $rule));

        return $rs;
    }

    /**
     * 对整型进行格式化
     *
     * @param mixed $value 变量值
     * @parma array $rule array('min' => '最小值', 'max' => '最大值')
     * @return int/string 格式化后的变量
     *
     */
    public static function formatInt($value, $rule)
    {
        $rs = intval($value);

        $rs = intval(self::filterByRange($rs, $rule));

        return $rs;
    }

    /**
     * 对浮点型进行格式化
     *
     * @param mixed $value 变量值
     * @parma array $rule array('min' => '最小值', 'max' => '最大值')
     * @return float/string 格式化后的变量
     *
     */
    public static function formatFloat($value, $rule)
    {
        $rs = floatval($value);

        $rs = floatval(self::filterByRange($rs, $rule));

        return $rs;
    }

    /**
     * 对布尔型进行格式化
     *
     * @param mixed $value 变量值
     * @parma array $rule array('true' => '成立时替换的内容', 'false' => '失败时替换的内容')
     * @return boolean/string 格式化后的变量
     *
     */
    public static function formatBoolean($value, $rule)
    {
        $rs = $value;

        if (!is_bool($value)) {
            if (is_numeric($value)) {
                $rs = $value > 0 ? true : false;
            } else if (is_string($value)) {
                $rs = in_array(strtolower($value), array('ok', 'true', 'success', 'on', 'yes')) 
                    ? true : false;
            } else {
                $rs = $value ? true : false;
            }
        }

        return $rs;
    }

    /**
     * 对日期进行格式化
     *
     * @param timestamp $value 变量值
     * @parma array $rule array('min' => '最小值', 'max' => '最大值')
     * @return timesatmp/string 格式化后的变量
     *
     */
    public static function formatDate($value, $rule)
    {
        $rs = $value;

        $format = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($format == 'timestamp') {
            $rs = strtotime($value);
            if ($rs === false || $rs < 0) {
            	$rs = 0;
            }
        }

        return $rs;
    }

    public static function formatArray($value, $rule)
    {
        $rs = $value;

        if (!is_array($rs)) {
            $format = !empty($rule['format']) ? strtolower($rule['format']) : '';
            if ($format == 'explode') {
                $rs = explode(isset($rule['separator']) ? $rule['separator'] : ',', $rs);
            } else if ($format == 'json') {
                $rs = json_decode($rs, true);
            } else {
                $rs = array($rs);
            }
        }

        return $rs;
    }

    /**
     * 检测枚举类型
     * @return 当不符合时返回null
     */
    public static function formatEnum($value, $rule)
    {
        if (!isset($rule['range']) || empty($rule['range']) || !is_array($rule['range'])) {
            throw new Core_Exception_RuleError(
                T("miss {name}'s enum range", array('name' => $rule['name'])));
        }

        if (!in_array($value, $rule['range'])) {
            throw new Core_Exception_IllegalParam(
                T('{name} should be in {range}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'range' => implode('/', $rule['range']), 'value' => $value))
            );
        }

        return $value;
    }

    /** ------------------ 加强自动检测，进行有效性过滤 ------------------ **/

    /**
     * 根据字符串长度进行截取
     */
    protected static function filterByStrLen($value, $rule)
    {
        $lenRule = $rule;
        $lenRule['name'] = $lenRule['name'] . '.len';
        $lenValue = strlen($value);
        self::filterByRange($lenValue, $lenRule);

        return $value;
    }

    /**
     * 根据范围进行控制
     */
    protected static function filterByRange($value, $rule)
    {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            throw new Core_Exception_RuleError(
                T('min should <= max, but now {name} min = {min} and max = {max}', 
                    array('name' => $rule['name'], 'min' => $rule['min'], 'max' => $rule['max']))
            );
        }

        if (isset($rule['min']) && $value < $rule['min']) {
            throw new Core_Exception_IllegalParam(
                T('{name} should >= {min}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'min' => $rule['min'], 'value' => $value))
            );
        }

        if (isset($rule['max']) && $value > $rule['max']) {
            throw new Core_Exception_IllegalParam(
                T('{name} should <= {max}, but now {name} = {value}', 
                array('name' => $rule['name'], 'max' => $rule['max'], 'value' => $value))
            );
        }

        return $value;
    }
}
