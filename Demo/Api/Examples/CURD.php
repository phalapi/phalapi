<?php
/**
 * 数据库CURD基本操作示例
 * @author dogstar 20170612
 */

class Api_Examples_CURD extends PhalApi_Api {

    public function getRules() {
        return array(
            'insert' => array(
                'title' => array('name' => 'title', 'require' => true, 'min' => 1, 'max' => '20', 'desc' => '标题'),
                'content' => array('name' => 'content', 'require' => true, 'min' => 1, 'desc' => '内容'),
                'state' => array('name' => 'state', 'type' => 'int', 'default' => 0, 'desc' => '状态'),
            ),
            'update' => array(
                'id' => array('name' => 'id', 'require' => true, 'min' => 1, 'desc' => 'ID'),
                'title' => array('name' => 'title', 'require' => true, 'min' => 1, 'max' => '20', 'desc' => '标题'),
                'content' => array('name' => 'content', 'require' => true, 'min' => 1, 'desc' => '内容'),
                'state' => array('name' => 'state', 'type' => 'int', 'default' => 0, 'desc' => '状态'),
            ),
            'get' => array(
                'id' => array('name' => 'id', 'require' => true, 'min' => 1, 'desc' => 'ID'),
            ),
            'delete' => array(
                'id' => array('name' => 'id', 'require' => true, 'min' => 1, 'desc' => 'ID'),
            ),
            'getList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default' => 1, 'desc' => '第几页'),
                'perpage' => array('name' => 'perpage', 'type' => 'int', 'min' => 1, 'max' => 20, 'default' => 10, 'desc' => '分页数量'),
                'state' => array('name' => 'state', 'type' => 'int', 'default' => 0, 'desc' => '状态'),
            ),
        );
    }

    /**
     * 插入数据
     * @desc 向数据库插入一条纪录数据
     * @return int id 新增的ID
     */
    public function insert() {
        $rs = array();

        $newData = array(
            'title' => $this->title,
            'content' => $this->content,
            'state' => $this->state,
        );

        $domain = new Domain_Examples_CURD();
        $id = $domain->insert($newData);

        $rs['id'] = $id;
        return $rs; 
    }

    /**
     * 更新数据
     * @desc 根据ID更新数据库中的一条纪录数据
     * @return int code 更新的结果，1表示成功，0表示无更新，false表示失败
     */
    public function update() {
        $rs = array();

        $newData = array(
            'title' => $this->title,
            'content' => $this->content,
            'state' => $this->state,
        );

        $domain = new Domain_Examples_CURD();
        $code = $domain->update($this->id, $newData);

        $rs['code'] = $code;
        return $rs;
    }

    /**
     * 获取数据
     * @desc 根据ID获取数据库中的一条纪录数据
     * @return int      id          主键ID
     * @return string   title       标题
     * @return string   content     内容
     * @return int      state       状态
     * @return string   post_date   发布日期
     */
    public function get() {
        $domain = new Domain_Examples_CURD();
        $data = $domain->get($this->id);

        return $data;
    }

    /**
     * 删除数据
     * @desc 根据ID删除数据库中的一条纪录数据
     * @return int code 删除的结果，1表示成功，0表示失败
     */
    public function delete() {
        $rs = array();

        $domain = new Domain_Examples_CURD();
        $code = $domain->delete($this->id);

        $rs['code'] = $code;
        return $rs;
    }

    /**
     * 获取分页列表数据
     * @desc 根据状态筛选列表数据，支持分页
     * @return array    items   列表数据
     * @return int      total   总数量
     * @return int      page    当前第几页
     * @return int      perpage 每页数量
     */
    public function getList() {
        $rs = array();

        $domain = new Domain_Examples_CURD();
        $list = $domain->getList($this->state, $this->page, $this->perpage);

        $rs['items'] = $list['items'];
        $rs['total'] = $list['total'];
        $rs['page'] = $this->page;
        $rs['perpage'] = $this->perpage;

        return $rs;
    }
}
