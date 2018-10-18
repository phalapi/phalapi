<?php
namespace PhalApi\Model;

use PhalApi\Model;
use PhalApi\Exception\InternalServerErrorException;

/**
 * PhalApi_Model_NotORM 基于NotORM的Model基类
 *
 * - 我们这里对ext_data使用json而不是序列化，是为了更容易阅读、理解、测试
 * - 可重写formatExtData() & parseExtData()重新定制针对序列化LOB的转换
 * - 具体子类需要实现getTableName($id)以提供对应的表名或者分表名
 * - 对于如何寻找表主键，我们将配置中全部的表(包括分表)对应的主键进行了装配，如果觉得有性能问题，可重写getTableKey($table)；这是一个手动和自动的问题
 *
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

class NotORMModel implements Model {

    protected static $tableKeys = array();

    public function get($id, $fields = '*') {
        $needFields = is_array($fields) ? implode(',', $fields) : $fields;
        $notorm = $this->getORM($id);

        $table = $this->getTableName($id);
        $rs = $notorm->select($needFields)
            ->where($this->getTableKey($table), $id)->fetch();

        $this->parseExtData($rs);

        return $rs;
    }

    public function insert($data, $id = NULL) {
        $this->formatExtData($data);

        $notorm = $this->getORM($id);
        $notorm->insert($data);

        return $notorm->insert_id();
    }

    public function update($id, $data) {
        $this->formatExtData($data);

        $notorm = $this->getORM($id);

        $table = $this->getTableName($id);
        return $notorm->where($this->getTableKey($table), $id)->update($data);
    }

    public function delete($id) {
        $notorm = $this->getORM($id);

        $table = $this->getTableName($id);
        return $notorm->where($this->getTableKey($table), $id)->delete();
    }

    /**
     * 对LOB的ext_data字段进行格式化(序列化)
     */
    protected function formatExtData(&$data) {
        if (isset($data['ext_data'])) {
            $data['ext_data'] = json_encode($data['ext_data']);
        }
    }

    /**
     * 对LOB的ext_data字段进行解析(反序列化)
     */
    protected function parseExtData(&$data) {
        if (isset($data['ext_data'])) {
            $data['ext_data'] = json_decode($data['ext_data'], true);
        }
    }

    /**
     * 根据主键值返回对应的表名，注意分表的情况
     * 
     * 默认表名为：[表前缀] + 全部小写的匹配表名
     *
     * 在以下场景下，需要重写此方法以指定表名
     * + 1. 自动匹配的表名与实际表名不符
     * + 2. 存在分表 
     * + 3. Model类名不含有Model_
     */
    protected function getTableName($id) {
        $className = get_class($this);
        $pos = strpos($className, '\\Model\\');

        $tableName = $pos !== FALSE ? substr($className, $pos + 7) : $className;
        $tableName = str_replace('\\', '_', trim($tableName, '//'));

        return strtolower($tableName);
    }

    /**
     * 根据表名获取主键名
     *
     * - 考虑到配置中的表主键不一定是id，所以这里将默认自动装配数据库配置并匹配对应的主键名
     * - 如果不希望因自动匹配所带来的性能问题，可以在每个实现子类手工返回对应的主键名
     * - 注意分表的情况
     * 
     * @param string $table 表名/分表名
     * @return string 主键名
     */
    protected function getTableKey($table) {
        if (empty(static::$tableKeys)) {
            $this->loadTableKeys();
        }

        return isset(static::$tableKeys[$table]) ? static::$tableKeys[$table] : static::$tableKeys['__default__'];
    }

    /**
     * 快速获取ORM实例，注意每次获取都是新的实例
     * @param string/int $id
     * @return \NotORM_Result
     */
    protected function getORM($id = NULL) {
        $table = $this->getTableName($id);
        return \PhalApi\DI()->notorm->$table;
    }

    /**
     * 快速获取指定table的ORM实例.
     * @param string $table 表名可指定服务器,比如demo2.user
     * @return \NotORM_Result
     */
    protected function table($table)
    {
        return \PhalApi\DI()->notorm->$table;
    }

    protected function loadTableKeys() {
        $tables = \PhalApi\DI()->config->get('dbs.tables');
        if (empty($tables)) {
            throw new InternalServerErrorException(\PhalApi\T('dbs.tables should not be empty'));
        }

        foreach ($tables as $tableName => $tableConfig) {
            static::$tableKeys[$tableName] = $tableConfig['key'];

            // 分表的主键
            foreach ($tableConfig['map'] as $mapItem) {
                if (!isset($mapItem['start']) || !isset($mapItem['end'])) {
                    continue;
                }

                for ($i = $mapItem['start']; $i <= $mapItem['end']; $i ++) {
                    static::$tableKeys[$tableName . '_' . $i] = $tableConfig['key'];
                }
            }
        }
    }
}
