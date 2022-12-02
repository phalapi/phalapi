<?php
namespace App\Api\Examples;

use PhalApi\Api;

/**
 * 接口示例
 * @author dogstar 20190325
 */
class Rule extends Api {

    public function getRules() {
        return array(
            // 字符串
            'str' => array(
                'str' => array('name' => 'str', 'desc' => '简单的字符串参数'),
            ),
            'defaultStr' => array(
                'str' => array('name' => 'str', 'type' => 'string', 'require' => true, 'default' => 'PhalApi', 'desc' => '默认字符串参数，且参数必须'),
                'strHide' => array('name' => 'str_hide', 'type' => 'string', 'require' => true, 'default' => 'PhalApi', 'desc' => '默认字符串参数，且参数必须', 'is_doc_hide' => true), // 接口文档隐藏参数，但实际仍然可使用
                'strRemove' => null, // 移除接口参数，在PHP后端代码中不可用，且不会在接口文档显示
            ),
            'regexStr' => array(
                'str' => array('name' => 'str', 'regex' => "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", 'desc' => '指定正则的字符串参数'),
            ),

            // 整数
            'number' => array(
                'number' => array('name' => 'number', 'type' => 'int', 'require' => true, 'desc' => '必须的整数参数'),
            ),
            'rangeNumber' => array(
                'number' => array('name' => 'number', 'type' => 'int', 'min' => 1, 'max' => 100, 'default' => 1, 'desc' => '指定范围且有默认值的整数参数'),
            ),

            // 浮点数，和整数类似，略……

            // 布尔值
            'trueOrFalse' => array(
                'switch' => array('name' => 'switch', 'type' => 'boolean', 'desc' => '以下值会转换为TRUE：ok，true，success，on，yes，1，以及其他PHP作为TRUE的值')
            ),

            // 日期
            'dateStr' => array(
                'date' => array('name' => 'date', 'type' => 'date', 'defaut' => '2019-03-01 00:00:00', 'desc' => '日期参数，没有强制的格式要求'),
            ),
            'dateTimestamp' => array(
                'date' => array('name' => 'date', 'type' => 'date', 'format' => 'timestamp', 'desc' => '会自动转为时间戳的日期参数')
            ),

            'jsonArray' => array(
                'datas' => array('name' => 'datas', 'type' => 'array', 'format' => 'json', 'default' => array(), 'desc' => 'JSON格式的数组参数，例如：datas={"name":"PhalApi"}'),
            ),
            'explodeArray' => array(
                'datas' => array('name' => 'datas', 'type' => 'array', 'format' => 'explode', 'default' => array(1, 2, 3), 'separator' => ',', 'min' => 1, 'max' => 10, 'desc' => '以英文逗号分割的数组，数组个数最少1个，最多10个，例如：datas=1,2,3'),
            ),

            // 枚举
            'sexEnum' => array(
                'sex' => array('name' => 'sex', 'type' => 'enum', 'range' => array('female', 'male'), 'desc' => '性别，female为女，male为男。'),
            ),
            'statusEnum' => array(
                'status' => array('name' => 'type', 'require' => true, 'type' => 'enum', 'range' => array('0', '1', '2'), 'desc' => '状态，注意：如果需要配置数值的枚举型，请使用字符串类型进行配置，避免误判。通常此时建议改用int整型。'),
            ),

            // 回调类型
            'versionCallback' => array(
                'version' => array('name' => 'version', 'require' => true, 'type' => 'callback', 'callback' => 'App\\Common\\Request\\Version::formatVersion', 'desc' => '版本号，指定回调函数进行检测，版本号格式例如：2.6.0。'),
            ),
        );
    }

    /** ---------------------- string 字符串参数 ---------------------- **/

    /**
     * 参数示例 - 字符串参数
     * @desc 简单的字符串参数
     */
    public function str() {
        return $this->str;
    }

    /**
     * 参数示例 - 默认且必须的字符串参数
     * @desc 带默认值，并且为必须的字符串参数。默认是string类型，所以一般不用配置type为string。
     */
    public function defaultStr() {
        return $this->str;
    }

    /**
     * 参数示例 - 正则字符串参数
     * @desc 指定正则的字符串参数
     */
    public function regexStr() {
        return $this->str;
    }

    /** ---------------------- int 整数参数 ---------------------- **/

    /**
     * 参数示例 - 整数参数
     * @desc 必须的整数参数
     */
    public function number() {
        return $this->number;
    }

    /**
     * 参数示例 - 指定范围的整数参数
     * @desc 指定范围的整数参数，可以设置最小值、最大值，当设定默认值时，对客户端则不是必传参数。
     */
    public function rangeNumber() {
        return $this->number;
    }

    /** ---------------------- boolean 布尔参数 ---------------------- **/

    /**
     * 参数示例 - 开关参数
     * @desc true或false的开关参数。
     */
    public function trueOrFalse() {
        return $this->switch;
    }

    /** ---------------------- date 日期参数 ---------------------- **/

    /**
     * 参数示例 - 日期参数
     * @desc 字符串的日期参数，例如格式：Y-m-d H:i:s
     */
    public function dateStr() {
        return $this->date;
    }

    /**
     * 参数示例 - 时间戳日期参数
     * @desc 时间戳日期参数，输入日期字符串参数，如传：2019-03-01 00:00:00，接口接收后会转成：1551369600。
     */
    public function dateTimestamp() {
        return $this->date;
    }

    /** ---------------------- array 数组参数 ---------------------- **/

    /**
     * 参数示例 - JSON数组参数
     * @desc 演示如何配置JSON格式的数组参数，并原路返回。
     */
    public function jsonArray() {
        return array('datas' => $this->datas);
    }

    /**
     * 参数示例 - 分割的数组参数
     * @desc 以英文逗号分割的数组，并且可以设置数组个数。
     */
    public function explodeArray() {
        return array('datas' => $this->datas);
    }

    /** ---------------------- enum 枚举参数 ---------------------- **/

    /**
     * 参数示例 - 枚举参数
     * @desc 例如，男或女的性别参数。
     */
    public function sexEnum() {
        return $this->sex;
    }

    /**
     * 参数示例 - 状态枚举参数
     * @desc 对于是数值的枚举范围，注意配置时请用字符串类型。
     */
    public function statusEnum() {
        return $this->status;
    }

    /** ---------------------- callable/callback 回调参数 ---------------------- **/

    /**
     * 参数示例 - 版本回调参数
     * @desc 回调类型的参数，可以自定义参数的检测、转换和校验逻辑。例如版本号。
     */
    public function versionCallback() {
        return $this->version;
    }
}
