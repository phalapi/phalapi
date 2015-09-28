<?php
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

abstract class PhalApi_Model_NotORM implements PhalApi_Model {

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
     */
    abstract protected function getTableName($id);

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
        if (empty(self::$tableKeys)) {
            $this->loadTableKeys();
        }

        return isset(self::$tableKeys[$table]) ? self::$tableKeys[$table] : self::$tableKeys['__default__'];
    }

    /**
     * 快速获取ORM实例，注意每次获取都是新的实例
     * @param string/int $id
     * @return NotORM
     */
    protected function getORM($id = NULL) {
        $table = $this->getTableName($id);
        return DI()->notorm->$table;
    }

    protected function loadTableKeys() {
        $tables = DI()->config->get('dbs.tables');
        if (empty($tables)) {
            throw new PhalApi_Exception_InternalServerError(T('dbs.tables should not be empty'));
        }

        foreach ($tables as $tableName => $tableConfig) {
            if (isset($tableConfig['start']) && isset($tableConfig['end'])) {
                for ($i = $tableConfig['start']; $i <= $tableConfig['end']; $i ++) {
                    self::$tableKeys[$tableName . '_' . $i] = $tableConfig['key'];
                }
            } else {
                self::$tableKeys[$tableName] = $tableConfig['key'];
            }
        }
    }
}
