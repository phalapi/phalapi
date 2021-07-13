<?php
namespace PhalApi\Model;

use PhalApi\Exception\InternalServerErrorException;

/**
 * 基于数据库的数据基类
 *
 * - 提供更常用的数据库操作接口，避免Model子类的重复开发工作
 * - 之所以不在NotORMModel扩展，是为了考虑避免对项目已有的接口有冲突和影响
 *
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2020-03-08
 */
class DataModel extends NotORMModel {

    /** ---------------- 获取实例 ---------------- **/

    /**
     * 创建并获取当前Model实例
     * @return \PhalApi\Model\DataModel 当前Model子类，继承于DataModel
     */
    public static function model() {
        return new static();
    }

    /**
     * 创建并获取当前Model对应的NotORM实例
     * @return NotORM_Result NotORM实例
     */
    public static function notorm() {
        $model = self::model();
        return $model->getORM();
    }

    /** ---------------- 更多数据库基本操作 ---------------- **/

    /** ---------------- 聚合操作 ---------------- **/

    /**
     * 获取总数
     * @param string|array|NULL $where 统计条件
     * @param string $countBy 需要统计的字段名
     * @return int 总数
     */
    public function count($where = NULL, $countBy = '*') {
        $orm = $this->getORM();

        // 条件
        if (!empty($where)) {
            $orm->where($where);
        }

        $total = $orm->count($countBy);
        return intval($total);
    }

    /**
     * 取最小值
     * @param string|array|NULL $where 统计条件
     * @param string $minBy 需要获取的字段
     * @return mixed
     */
    public function min($where, $minBy) {
        return $this->getORM()->where($where)->min($minBy);
    }

    /**
     * 取最大值
     * @param string|array|NULL $where 统计条件
     * @param string $maxBy 需要获取的字段
     * @return mixed
     */
    public function max($where, $maxBy) {
        return $this->getORM()->where($where)->max($maxBy);
    }

    /**
     * 求和
     * @param string|array $where 查询条件，例如：id = 1，或数组形式array('id' => 1)
     * @param string $sumBy 求和字段
     * @return int 和
     */
    public function sum($where, $sumBy) {
        return $this->getORM()->where($where)->sum($sumBy);
    }

    /** ---------------- 查询操作 ---------------- **/

    /**
     * 获取字段值
     * @param string $field 需要查询的字段名，通常为主键或带有唯一索引的字段
     * @param string|int|float|NULL $value 查询的值
     * @param string $selectFiled 需要获取的字段
     * @param mixed $default 默认值
     * @return mixed 字段值
     */
    public function getValueBy($field, $value, $selectFiled, $default = FALSE) {
        $rows = $this->getValueMoreBy($field, $value, $selectFiled, 1);
        return $rows ? $rows[0] : $default;
    }

    /**
     * 获取字段值（多个）
     * @param string $field 需要查询的字段名，通常为主键或带有唯一索引的字段
     * @param string|array|int|float|NULL $value 查询的值
     * @param string $selectFiled 需要获取的字段
     * @param int $limit 需要获取的数量，为0时无限制，顺序获取
     * @param boolean $isDistinct 是否去重
     * @return array 字段值数组，没有时返回空数组
     */
    public function getValueMoreBy($field, $value, $selectFiled, $limit = 0, $isDistinct = FALSE) {
        $orm = $this->getORM()->select($isDistinct ? 'DISTINCT ' : '' . $selectFiled)->where($field, $value);
        $limit = intval($limit);
        if ($limit > 0) {
            $orm->limit(0, $limit);
        }
        $rows = $orm->fetchAll();
        return $rows ? array_column($rows, $selectFiled) : array();
    }

    /**
     * 获取一条纪录
     * @param string $field 需要查询的字段名，通常为主键或带有唯一索引的字段
     * @param string|int|float|NULL $value 查询的值
     * @param string|array $select 需要获取的字段
     * @param boolean|array $default 默认值
     * @return array|boolean 没有时返回默认值
     */
    public function getDataBy($field, $value, $select = '*', $default = FALSE) {
        $rows = $this->getDataMoreBy($field, $value, 1, $select);
        return !empty($rows) ? $rows[0] : $default; 
    }

    /**
     * 获取多条纪录
     * @param string $field 需要查询的字段名，通常为主键或带有唯一索引的字段
     * @param string|int|float|NULL $value 查询的值
     * @param int $limit 需要获取的数量，为0时无限制，顺序获取
     * @param string|array $select 需要获取的字段
     * @return array 没有时返回空数组
     */
    public function getDataMoreBy($field, $value, $limit = 0, $select = '*') {
        $orm = $this->getORM()
            ->select(is_array($select) ? implode(',', $select) : $select)
            ->where($field, $value);
        $limit = intval($limit);
        if ($limit > 0) {
            $orm->limit(0, $limit);
        }
        return $orm->fetchAll();
    }

    /**
     * 根据条件，取一条纪录数据
     * @param string|array $where 查询条件
     * @param array $whereParams 更复杂查询条件时的动态参数
     * @param string|array $select 需要获取的字段
     * @param boolean|array $default 默认值
     * @return array|boolean 没有时返回默认值
     */
    public function getData($where = NULL, $whereParams = array(), $select = '*', $default = FALSE) {
        $rows = $this->getList($where, $whereParams, $select, NULL, 1, 1);
        return !empty($rows) ? $rows[0] : $default;
    }

    /**
     * 根据条件，取列表数组
     * @param string|array $where 查询条件
     * @param array $whereParams 更复杂查询条件时的动态参数
     * @param string|array $select 需要获取的字段
     * @param string $order 排序
     * @param int $page 第几页
     * @param int $perpage 分页数量
     * @return array 没有时返回空数组
     */
    public function getList($where = NULL, $whereParams = array(), $select = '*', $order = NULL, $page = 1, $perpage = 100) {
        $page = intval($page);
        $perpage = intval($perpage);

        $orm = $this->getORM();

        // 条件
        if (!empty($where) && !empty($whereParams)) {
            $orm->where($where, $whereParams);
        } else if (!empty($where)) {
            $orm->where($where);
        }

        // 字段选择
        $select = is_array($select) ? implode(',', $select) : $select;
        $orm->select($select);

        // 排序
        $order = is_array($order) ? implode(', ', $order) : $order;
        if (!empty($order)) {
            $orm->order($order);
        }

        // 分页
        return $orm->page($page, $perpage)->fetchAll();
    }

    public function __call($name, $arguments) {
        if (substr($name, 0, 9) == 'getDataBy') {
            $field = lcfirst(substr($name, 9));
            $value = isset($arguments[0]) ? $arguments[0] : NULL;
            $select = isset($arguments[1]) ? $arguments[1] : '*';
            $default = isset($arguments[2]) ? $arguments[2] : FALSE;
            return $this->getDataBy($field, $value, $select, $default);
        } else if (substr($name, 0, 13) == 'getDataMoreBy') {
            $field = lcfirst(substr($name, 13));
            $value = isset($arguments[0]) ? $arguments[0] : NULL;
            $limit = isset($arguments[1]) ? $arguments[1] : 0;
            $select = isset($arguments[2]) ? $arguments[2] : '*';
            return $this->getDataMoreBy($field, $value, $limit, $select);
        }

        throw new InternalServerErrorException(
            \PhalApi\T('Error: Call to undefined function PhalApi\Model\DataModel::{name}()', array('name' => $name))
        );

    }

    /** ---------------- 删除操作 ---------------- **/

    /**
     * 删除全部
     * @param string|array $where 查询条件，例如：id = 1，或数组形式array('id' => 1)
     * @return int|boolean 返回删除的条数
     */
    public function deleteAll($where) {
        return $this->getORM()->where($where)->delete();
    }

    /**
     * 根据多个ID删除
     * @param string|array $ids
     * @return int|boolean 返回删除的条数
     */
    public function deleteIds($ids) {
        return $this->getORM()->where('id', $ids)->delete();
    }

    /** ---------------- 更新操作 ---------------- **/

    /**
     * 更新全部数据
     * @param string|array $where 查询条件，例如：id = 1，或数组形式array('id' => 1)
     * @param array $updateData
     * @return int|boolean 返回更新的条数
     */
    public function updateAll($where, array $updateData) {
        return $this->getORM()->where($where)->update($updateData);
    } 

    /**
     * 更新计数器
     * @param string|array $where 查询条件，例如：id = 1，或数组形式array('id' => 1)
     * @param array $updateData 累加或累减的更新数据，例如：array('字段名' => 1)，支持多组
     * @return int|boolean 返回更新的条数
     */
    public function updateCounter($where, array $updateData) {
        return $this->getORM()->where($where)->updateMultiCounters($updateData);
    }

    /** ---------------- 插入操作 ---------------- **/

    public function insert($data, $id = NULL) {
        $id = parent::insert($data, $id);
        return $id !== FALSE ? intval($id) : $id;
    }

    /**
     * 批量插入
     * @param array $datas 二维数组
     * @param string $isIgnore
     * @return int 返回新增的条数
     */
    public function insertMore($datas, $isIgnore = FALSE) {
        return $this->getORM()->insert_multi($datas, $isIgnore);
    }

    /** ---------------- SQL原生操作 ---------------- **/

    /**
     * 执行SQL查询语句，支持参数绑定
     * @param string $sql 完整的查询语句，例如：select * from user where id = :id，或：select * from user where id = ？
     * @param array $parmas 需要动态绑定的参数，例如：array(':id' => 1)，或：array(1)
     * @return array 查询的结果集
     * @throws PDOException
     */
    public function queryAll($sql, $parmas = array()) {
        return $this->getORM()->queryAll($sql, $parmas);
    }

    /**
     * 执行SQL变更语句，支持参数绑定
     * @param string $sql 完整的变更语句
     * @param array $params 需要动态绑定的参数，例如：array(':id' => 1)，或：array(1)
     * @return int|boolean 返回影响的行数
     * @throws PDOException
     */
    public function executeSql($sql, $params = array()) {
        return $this->getORM()->executeSql($sql, $params);
    }

}
