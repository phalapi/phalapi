<?php
namespace Portal\Common;

use PhalApi\Exception\BadRequestException;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 通用数据接口
 * @author dogstra 20200309
 */
class DataApi extends Api {

    protected function getDataModel() {
        throw new InternalServerErrorException(__CLASS__ . '接口类必须先实现getDataModel()方法，返回具体的Model数据子类对象');
    }

    public function getRules() {
        return array(
            'tableList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => '第几页'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => 20, 'min' => 1, 'max' => 1000, 'desc' => '分页数量'),
                'searchParams' => array('name' => 'searchParams', 'type' => 'array', 'format' => 'json', 'default' => array(), 'desc' => '搜索条件'),
            ),
            'createData' => array(
                'newData' => array('name' => 'newData', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => '需要创建的数据'),
            ),
            'deleteDataIDs' => array(
                'ids' => array('name' => 'ids', 'type' => 'array', 'format' => 'explode', 'seperator' => ',', 'require' => true, 'desc' => '待删除的多个ID，用英文逗号分割')
            ),
            'getData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => 'ID'),
            ),
            'updateData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => 'ID'),
                'data' =>  array('name' => 'data', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => '需要更新的数据'),
            ),
        );
    }

    /**
     * 获取表格列表数据
     * @desc 获取表格列表数据，默认按ID降序返回，支持分页和搜索
     */
    public function tableList() {
        $model = $this->getDataModel();

        $searchParams = array();
        foreach ($this->searchParams as $key => $val) {
            if ($val !==  '') {
                $searchParams[$key] = $val;
            }
        }
        $where = !empty($searchParams) ? $searchParams : NULL;
        $whereParams = array();
        $select = $this->getTableListSelect();
        $order = $this->getTableListOrder();
        $page = $this->page;
        $perpage = $this->limit;

        $total = $model->count($where);
        $items = $total > 0 ? $model->getList($where, $whereParams, $select, $order, $page, $perpage) : array();

        return array('total' => $total, 'items' => $items);
    }

    // 列表返回的字段
    protected function getTableListSelect() {
        return '*';
    }

    // 列表的默认排序
    protected function getTableListOrder() {
        return 'id DESC';
    }
    
    // 取到列表数据后的加工处理
    protected function afterTableList($items) {
        return $items;
    }
    
    /**
     * 创建新数据
     * @desc 创建一条新数据
     * @return int id 新纪录的ID
     */
    public function createData() {
        $model = $this->getDataModel();
        
        $newData = $this->newData;
        // 检测必传字段
        foreach ($this->createDataRequireKeys() as $key) {
            if (!isset($newData[$key]) || $newData[$key] === '') {
                throw new BadRequestException('缺少必传字段：' . $key);
            }
        }
        // 排除字段
        foreach ($this->createDataExcludeKeys() as $key) {
            unset($newData[$key]);
        }
        
        // 更多初始化的字段数据
        $newData = $this->createDataMoreData($newData);
        
        if (empty($newData)) {
            throw new BadRequestException('缺少创建的数据');
        }
        
        $id = 0;
        try {
            $id = $model->insert($newData);
        } catch (\PDOException $ex) {
            throw new BadRequestException(\PhalApi\DI()->debug ? $ex->getMessage() : '添加失败，请联系技术人员');
        }
        
        return array('id' => intval($id));
    }
    
    // 必须提供的字段
    protected function createDataRequireKeys() {
        return array();
    }
    
    // 不允许客户端写入的字段
    protected function createDataExcludeKeys() {
        return array();
    }
    
    // 创建时更多初始化的数据
    protected function createDataMoreData($newData) {
        return $newData;
    }
    
    /**
     * 批量删除
     * @desc 根据ID批量删除数据
     */
    public function deleteDataIDs() {
        $model = $this->getDataModel();
        $rows = $this->ids ? $model->deleteIds($this->ids) : 0;
        return array('deleted_num' => $rows);
    }
    
    /**
     * 获取一条数据
     * @desc 根据ID获取一条数据
     * @return object|null 数据
     */
    public function getData() {
        $model = $this->getDataModel();
        $data = $model->get($this->id, $this->getDataSelect());
        
        $data = $this->afterGetData($data);
        
        return array('data' => $data ? $data : null);
    }
    
    // 获取单个数据时需要返回的字段
    protected function getDataSelect() {
        return '*';
    }
    
    // 取到数据后的加工处理
    protected function afterGetData($data) {
        return $data;
    }
    
    /**
     * 更新数据
     * @desc 根据ID更新单条数据
     * @return int|boolean updated_num 更新的数据条数，0表示无更新，1表示更新成功
     */
    public function updateData() {
        $model = $this->getDataModel();
        $updateData = $this->data;
        
        unset($updateData['id']);
        
        foreach ($this->updateDataRequireKeys() as $key) {
            if (!isset($updateData[$key]) || $updateData[$key] === '') {
                throw new BadRequestException('缺少必传字段：' . $key);
            }
        }
        
        foreach ($this->updateDataExcludeKeys() as $key) {
            unset($updateData[$key]);
        }
        
        if (empty($updateData)) {
            throw new BadRequestException('缺少更新的数据');
        }
        
        try {
            $rows = $model->update($this->id, $updateData);
            return array('updated_num' => $rows);
        } catch (\PDOException $ex) {
            throw new BadRequestException(\PhalApi\DI()->debug ? $ex->getMessage() : '添加失败，请联系技术人员');
        }
    }
    
    // 更新时必须提供的字段
    protected function updateDataRequireKeys() {
        return array();
    }
    
    // 更新时不允许更新的字段
    protected function updateDataExcludeKeys() {
        return array();
    }
    
    protected function beforeUpdateData($updateData) {
        return $updateData;
    }
}
