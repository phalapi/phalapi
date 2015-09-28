<?php
/**
 * PhalApi_Request_Var 变量格式化类
 *
 * 针对设定的规则进行对品购模块中的变量进行格式化操作
 * 
 * - 1、根据字段与预定义变量对应关系，获取变量值
 * - 2、对变量进行类型转换
 * - 3、进行有效性判断过滤
 * - 4、按业务需求进行格式化 
 * 
 * <br>格式规则：<br>
```
 *  array('name' => '', 'type' => 'string', 'default' => '', 'min' => '', 'max' => '', 'regex' => '')
 *  array('name' => '', 'type' => 'int', 'default' => '', 'min' => '', 'max' => '',)
 *  array('name' => '', 'type' => 'float', 'default' => '', 'min' => '', 'max' => '',)
 *  array('name' => '', 'type' => 'boolean', 'default' => '',)
 *  array('name' => '', 'type' => 'date', 'default' => '',)
 *  array('name' => '', 'type' => 'array', 'default' => '', 'format' => 'json/explode', 'separator' => '')
 *  array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
 *  array('name' => '', 'type' => 'file', 'default' => array(...), 'min' => '', 'max' => '', 'range' => array(...))
```
 *
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class PhalApi_Request_Var {

    /** ------------------ 对外开放操作 ------------------ **/

    /**
     * 统一格式化操作
     * 扩展参数请参见各种类型格式化操作的参数说明
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
     * @param array $params 参数列表
     * @return miexd 格式后的变量
     */ 
    public static function format($varName, $rule, $params) {
        $value = isset($rule['default']) ? $rule['default'] : NULL;
        $type = !empty($rule['type']) ? strtolower($rule['type']) : 'string';

        $key = isset($rule['name']) ? $rule['name'] : $varName;
        $value = isset($params[$key]) ? $params[$key] : $value;

        if ($value === NULL && $type != 'file') { //排除文件类型
            return $value;
        }

        return self::formatAllType($type, $value, $rule);
    }

    /**
     * 统一分发处理
     * @param string $type 类型
     * @param string $value 值
     * @param array $rule 规则配置
     * @return mixed
     */
    protected static function formatAllType($type, $value, $rule) {
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
                $value = self::formatBoolean($value);
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
			//文件类型
            case 'file':
                $value = self::formatFile($rule);
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
     * @@param array $rule array('len' => ‘最长长度’)
     * @return string 格式化后的变量
     *
     */
    public static function formatString($value, $rule) {
        $rs = strval(self::filterByStrLen(strval($value), $rule));

        self::filterByRegex($rs, $rule);

        return $rs;
    }

    /**
     * 进行正则匹配
     */
    protected static function filterByRegex($value, $rule) {
        if (!isset($rule['regex']) || empty($rule['regex'])) {
            return;
        }

        //如果你看到此行报错，说明提供的正则表达式不合法
        if (preg_match($rule['regex'], $value) <= 0) {
            throw new PhalApi_Exception_BadRequest(
                T('{name} can not match {regex}', array('name' => $rule['name'], 'regex' => $rule['regex']))
            );
        }
    }

    /**
     * 对整型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule array('min' => '最小值', 'max' => '最大值')
     * @return int/string 格式化后的变量
     *
     */
    public static function formatInt($value, $rule) {
        return intval(self::filterByRange(intval($value), $rule));
    }

    /**
     * 对浮点型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule array('min' => '最小值', 'max' => '最大值')
     * @return float/string 格式化后的变量
     *
     */
    public static function formatFloat($value, $rule) {
        return floatval(self::filterByRange(floatval($value), $rule));
    }

    /**
     * 对布尔型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule array('TRUE' => '成立时替换的内容', 'FALSE' => '失败时替换的内容')
     * @return boolean/string 格式化后的变量
     *
     */
    public static function formatBoolean($value) {
        $rs = $value;

        if (!is_bool($value)) {
            if (is_numeric($value)) {
                $rs = $value > 0 ? TRUE : FALSE;
            } else if (is_string($value)) {
                $rs = in_array(strtolower($value), array('ok', 'true', 'success', 'on', 'yes')) 
                    ? TRUE : FALSE;
            } else {
                $rs = $value ? TRUE : FALSE;
            }
        }

        return $rs;
    }

    /**
     * 对日期进行格式化
     *
     * @param timestamp $value 变量值
     * @param array $rule array('min' => '最小值', 'max' => '最大值')
     * @return timesatmp/string 格式化后的变量
     *
     */
    public static function formatDate($value, $rule) {
        $rs = $value;

        $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($ruleFormat == 'timestamp') {
            $rs = strtotime($value);
            if ($rs <= 0) {
            	$rs = 0;
            }
        }

        return $rs;
    }

    /**
     * 对数组格式化/数组转换
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'array', 'default' => '', 'format' => 'json/explode', 'separator' => '')
     * @return array
     */
    public static function formatArray($value, $rule) {
        $rs = $value;

        if (!is_array($rs)) {
            $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
            if ($ruleFormat == 'explode') {
                $rs = explode(isset($rule['separator']) ? $rule['separator'] : ',', $rs);
            } else if ($ruleFormat == 'json') {
                $rs = json_decode($rs, TRUE);
            } else {
                $rs = array($rs);
            }
        }

        return $rs;
    }

    /**
     * 检测枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @return 当不符合时返回$rule
     */
    public static function formatEnum($value, $rule) {
        self::formatEnumRule($rule);

        self::formatEnumValue($value, $rule);

        return $value;
    }

    /**
     * 检测枚举规则的合法性
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws PhalApi_Exception_InternalServerError
     */
    protected static function formatEnumRule($rule) {
        if (!isset($rule['range'])) {
            throw new PhalApi_Exception_InternalServerError(
                T("miss {name}'s enum range", array('name' => $rule['name'])));
        }

        if (empty($rule['range']) || !is_array($rule['range'])) {
            throw new PhalApi_Exception_InternalServerError(
                T("{name}'s enum range can not be empty", array('name' => $rule['name'])));
        }
    }

    /**
     * 格式化枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws PhalApi_Exception_BadRequest
     */
    protected static function formatEnumValue($value, $rule) {
        if (!in_array($value, $rule['range'])) {
            throw new PhalApi_Exception_BadRequest(
                T('{name} should be in {range}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'range' => implode('/', $rule['range']), 'value' => $value))
            );
        }
    }

	/**
	 * 格式化文件类型
     * @param array $rule array('name' => '', 'type' => 'file', 'default' => array(...), 'min' => '', 'max' => '', 'range' => array(...))
     * @throws PhalApi_Exception_BadRequest
	 */
    public static function formatFile($rule) {
        $default = isset($rule['default']) ? $rule['default'] : NULL;

        $index = $rule['name'];
        if (!isset($_FILES[$index]) && $default !== NULL) {
            return $default;
        }

        if (!isset($_FILES[$index]) || !isset($_FILES[$index]['error']) || is_array($_FILES[$index]['error'])) {
            throw new PhalApi_Exception_BadRequest(
                T('miss upload file: {file}', array('file' => $index)));
        }

        if ($_FILES[$index]['error'] != UPLOAD_ERR_OK) {
            throw new PhalApi_Exception_BadRequest(
                T('fail to upload file with error = {error}', array('error' => $_FILES[$index]['error'])));
        }

        $sizeRule = $rule;
        $sizeRule['name'] = $sizeRule['name'] . '.size';
        self::filterByRange($_FILES[$index]['size'], $sizeRule);

        if (!empty($rule['range']) && is_array($rule['range'])) {
		    $rule['range'] = array_map('strtolower', $rule['range']);
            self::formatEnumValue(strtolower($_FILES[$index]['type']), $rule);
        }

        return $_FILES[$index];
    }

    /** ------------------ 加强自动检测，进行有效性过滤 ------------------ **/

    /**
     * 根据字符串长度进行截取
     */
    protected static function filterByStrLen($value, $rule) {
        $lenRule = $rule;
        $lenRule['name'] = $lenRule['name'] . '.len';
        $lenValue = strlen($value);
        self::filterByRange($lenValue, $lenRule);

        return $value;
    }

    /**
     * 根据范围进行控制
     */
    protected static function filterByRange($value, $rule) {
        self::filterRangeMinLessThanOrEqualsMax($rule);

        self::filterRangeCheckMin($value, $rule);

        self::filterRangeCheckMax($value, $rule);

        return $value;
    }

    protected static function filterRangeMinLessThanOrEqualsMax($rule) {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            throw new PhalApi_Exception_InternalServerError(
                T('min should <= max, but now {name} min = {min} and max = {max}', 
                    array('name' => $rule['name'], 'min' => $rule['min'], 'max' => $rule['max']))
            );
        }
    }

    protected static function filterRangeCheckMin($value, $rule) {
        if (isset($rule['min']) && $value < $rule['min']) {
            throw new PhalApi_Exception_BadRequest(
                T('{name} should >= {min}, but now {name} = {value}', 
                    array('name' => $rule['name'], 'min' => $rule['min'], 'value' => $value))
            );
        }
    }

    protected static function filterRangeCheckMax($value, $rule) {
        if (isset($rule['max']) && $value > $rule['max']) {
            throw new PhalApi_Exception_BadRequest(
                T('{name} should <= {max}, but now {name} = {value}', 
                array('name' => $rule['name'], 'max' => $rule['max'], 'value' => $value))
            );
        }
    }
}
