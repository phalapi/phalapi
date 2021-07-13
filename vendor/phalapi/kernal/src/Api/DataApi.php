<?php
namespace PhalApi\Api;

use PhalApi\Api;
use PhalApi\Exception\BadRequestException;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 通用数据接口
 *
 * - 进驻内核
 * - 以更少的代码，实现更多的接口，满足更广业务需求
 *
 * @author dogstra 20200313
 */
class DataApi extends Api {

    /**
     * 获取数据模型实例
     * - 接口子类必须实现此方法，返回继承于PhalApi\Model\DataModel的Model数据模型子类
     * @throws PhalApi\Exception\InternalServerErrorException
     * @return PhalApi\Model\DataModel
     */
    protected function getDataModel() {
        throw new InternalServerErrorException(__CLASS__ . \PhalApi\T('must implement getDataModel() to return your model instance'));
    }

    /**
     * 用户身份验证
     * - 为保护接口不受外部非法调用，接口子类必须明确重载此方法，进行用户身份验证
     * - 如果确实不需要进行身份验证，重载后可进行空操作
     * @throws PhalApi\Exception\BadRequestException 当验证失败时，请抛出此异常，以返回400
     */
    protected function userCheck() {
        throw new InternalServerErrorException(__CLASS__ . \PhalApi\T('must implement userCheck() to protected your API'));
    }

    public function getRules() {
        return array(
            'tableList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => \PhalApi\T('page')),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => 20, 'min' => 1, 'max' => 1000, 'desc' => \PhalApi\T('page limit')),
                'searchParams' => array('name' => 'searchParams', 'type' => 'array', 'format' => 'json', 'default' => array(), 'desc' => \PhalApi\T('search params')),
            ),
            'createData' => array(
                'newData' => array('name' => 'newData', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => \PhalApi\T('post data')),
            ),
            'deleteData' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'min' => 1, 'desc' => \PhalApi\T('ids to delete')),
            ),
            'deleteDataIDs' => array(
                'ids' => array('name' => 'ids', 'type' => 'array', 'format' => 'explode', 'seperator' => ',', 'require' => true, 'desc' => \PhalApi\T('ids to delete'))
            ),
            'getData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => 'ID'),
            ),
            'updateData' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => 'ID'),
                'data' =>  array('name' => 'data', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => \PhalApi\T('data to update')),
            ),
        );
    }

    /**
     * 获取表格列表数据
     * @desc 通用数据接口，获取表格列表数据，默认按ID降序返回，支持分页和搜索
     */
    public function tableList() {
        $model = $this->getDataModel();

        $searchParams = array();
        foreach ($this->searchParams as $key => $val) {
            if ($val !==  '') {
                $searchParams[$key] = $val;
            }
        }

        // 条件
        $where = !empty($searchParams) ? $searchParams : NULL;
        $whereParams = array();
        $where = $this->getTableListWhere($where);

        $select = $this->getTableListSelect();
        $order = $this->getTableListOrder();
        $page = $this->page;
        $perpage = $this->limit;

        $total = $model->count($where);
        $items = $total > 0 ? $model->getList($where, $whereParams, $select, $order, $page, $perpage) : array();
        $items = $this->afterTableList($items);

        return $this->returnDataResult(array('total' => $total, 'items' => $items));
    }

    // 列表返回的字段
    protected function getTableListSelect() {
        return '*';
    }

    // 列表的默认排序
    protected function getTableListOrder() {
        return 'id DESC';
    }

    // 查询条件
    protected function getTableListWhere($where) {
        return $where;
    }
    
    // 取到列表数据后的加工处理
    protected function afterTableList($items) {
        return $items;
    }
    
    /**
     * 创建新数据
     * @desc 通用数据接口，创建一条新数据
     * @return int id 新纪录的ID
     */
    public function createData() {
        $model = $this->getDataModel();
        
        $newData = $this->newData;
        // 检测必传字段
        foreach ($this->createDataRequireKeys() as $key) {
            if (!isset($newData[$key]) || $newData[$key] === '') {
                throw new BadRequestException(\PhalApi\T('{name} require, but miss', array('name' => $key)));
            }
        }
        // 排除字段
        foreach ($this->createDataExcludeKeys() as $key) {
            unset($newData[$key]);
        }
        
        // 更多初始化的字段数据
        $newData = $this->beforeCreateData($newData);
        
        if (empty($newData)) {
            throw new BadRequestException(\PhalApi\T('miss post data'));
        }
        
        $id = 0;
        try {
            $id = $model->insert($newData);
        } catch (\PDOException $ex) {
            throw new BadRequestException(\PhalApi\DI()->debug ? $ex->getMessage() : \PhalApi\T('system error, please contact engeneer'));
        }
        
        return $this->returnDataResult(array('id' => intval($id)));
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
    protected function beforeCreateData($newData) {
        return $newData;
    }

    /**
     * 根据ID删除数据
     * @desc 根据单个ID删除数据，也可以调整成根据自定义的条件删除数据
     */
    public function deleteData() {
        $where = array('id' => $this->id);

        $model = $this->getDataModel();
        $rows = !empty($where) ? $model->deleteAll($where) : 0;

        return $this->returnDataResult(array('deleted_num' => $rows));
    }

    // 删除数据的条件加工
    protected function getDeleteDataWhere($where) {
        return $where;
    }
    
    /**
     * 批量删除
     * @desc 通用数据接口，根据ID批量删除数据
     */
    public function deleteDataIDs() {
        $model = $this->getDataModel();
        $rows = $this->ids ? $model->deleteIds($this->ids) : 0;
        return $this->returnDataResult(array('deleted_num' => $rows));
    }
    
    /**
     * 获取一条数据
     * @desc 通用数据接口，根据ID获取一条数据
     * @return object|null 数据
     */
    public function getData() {
        $where = array('id' => $this->id);
        $whereParams = array();
        $select = $this->getDataSelect();

        // 前置条件加工
        $where = $this->getGetDataWhere($where);

        $model = $this->getDataModel();
        $data = $model->getData($where, $whereParams, $select);
        
        $data = $this->afterGetData($data);
        
        return $this->returnDataResult(array('data' => $data ? $data : null));
    }
    
    // 获取单个数据时需要返回的字段
    protected function getDataSelect() {
        return '*';
    }
    
    // 前置条件加工
    protected function getGetDataWhere($where) {
        return $where;
    }

    // 取到数据后的加工处理
    protected function afterGetData($data) {
        return $data;
    }
    
    /**
     * 更新数据
     * @desc 通用数据接口，根据ID更新单条数据
     * @return int|boolean updated_num 更新的数据条数，0表示无更新，1表示更新成功
     */
    public function updateData() {
        $model = $this->getDataModel();
        $updateData = $this->data;
        
        unset($updateData['id']);
        
        foreach ($this->updateDataRequireKeys() as $key) {
            if (!isset($updateData[$key]) || $updateData[$key] === '') {
                throw new BadRequestException(\PhalApi\T('{name} require, but miss', array('name' => $key)));
            }
        }
        
        foreach ($this->updateDataExcludeKeys() as $key) {
            unset($updateData[$key]);
        }
        
        if (empty($updateData)) {
            throw new BadRequestException(\PhalApi\T('miss update data'));
        }
        
        $updateData = $this->beforeUpdateData($updateData);

        try {
            $where = array('id' => $this->id);
            $where = $this->getUpdateDataWhere($where);

            $rows = $model->updateAll($where, $updateData);
            return $this->returnDataResult(array('updated_num' => $rows));
        } catch (\PDOException $ex) {
            throw new BadRequestException(\PhalApi\DI()->debug ? $ex->getMessage() : \PhalApi\T('system error, please contact engeneer'));
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

    // 获取更新数据的条件
    protected function getUpdateDataWhere($where) {
        return $where;
    }
    
    // 在更新数据前的处理
    protected function beforeUpdateData($updateData) {
        return $updateData;
    }

    /**
     * 返回数据结果
     * - 方便统一进行加工再处理
     * @param array|mixed $result 等返回的数据结果
     * @return array|mixed 加工后的数据结果
     */
    protected function returnDataResult($result) {
        return $result;
    }
}
