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
        $diKey = '_formatter' . ucfirst($type);
        $diDefautl = 'PhalApi_Request_Formatter_' . ucfirst($type);

        $formatter = DI()->get($diKey, $diDefautl);

        if (!($formatter instanceof Phalapi_Request_Formatter)) {
            throw new PhalApi_Exception_InternalServerError(
                T('invalid type: {type} for rule: {name}', array('type' => $type, 'name' => $rule['name']))
            );
        }

        return $formatter->parse($value, $rule);
    }
}
