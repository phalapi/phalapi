<?php
namespace App\Api\Examples;

use PhalApi\Api;
use App\Domain\Examples\CURD as DomainCURD;

/**
 * 接口示例
 * @author dogstar 20170612
 */

class CURD extends Api {

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
     * 数据库示例 - 插入数据
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

        $domain = new DomainCURD();
        $id = $domain->insert($newData);

        $rs['id'] = $id;
        return $rs; 
    }

    /**
     * 数据库示例 - 更新数据
     * @desc 根据ID更新数据库中的一条纪录数据
     * @method POST
     * @return int code 更新的结果，1表示成功，0表示无更新，false表示失败
     */
    public function update() {
        $rs = array();

        $newData = array(
            'title' => $this->title,
            'content' => $this->content,
            'state' => $this->state,
        );

        $domain = new DomainCURD();
        $code = $domain->update($this->id, $newData);

        $rs['code'] = $code;
        return $rs;
    }

    /**
     * 数据库示例 - 获取数据
     * @desc 根据ID获取数据库中的一条纪录数据
     * @method GET
     * @return int      id          主键ID
     * @return string   title       标题
     * @return string   content     内容
     * @return int      state       状态
     * @return string   post_date   发布日期
     */
    public function get() {
        $domain = new DomainCURD();
        $data = $domain->get($this->id);

        return $data;
    }

    /**
     * 数据库示例 - 删除数据
     * @desc 根据ID删除数据库中的一条纪录数据
     * @return int code 删除的结果，1表示成功，0表示失败
     */
    public function delete() {
        $rs = array();

        $domain = new DomainCURD();
        $code = $domain->delete($this->id);

        $rs['code'] = $code;
        return $rs;
    }

    /**
     * 数据库示例 - 获取分页列表数据
     * @desc 根据状态筛选列表数据，支持分页
     * @return array    items   列表数据
     * @return int      total   总数量
     * @return int      page    当前第几页
     * @return int      perpage 每页数量
     */
    public function getList() {
        $rs = array();

        $domain = new DomainCURD();
        $list = $domain->getList($this->state, $this->page, $this->perpage);

        $rs['items'] = $list['items'];
        $rs['total'] = $list['total'];
        $rs['page'] = $this->page;
        $rs['perpage'] = $this->perpage;

        return $rs;
    }

    /**
     * 数据库示例 - 演示如何进行SQL调试和相关的使用
     * @desc 除此接口外，其他示例也可进行在线调试。本示例将便详细说明如何调试。
     */
    public function sqlDebug() {
        $rs = array();

        // 当需要进行sql调试时，请先开启sys.debug和sys.notorm_debug，设置为true

        // 以下是操作数据库部分
        // 第一种，你可以直接在API层或任何地方使用全局方式操作数据库（但不推荐！）
        $rs['row_1'] = \PhalApi\DI()->notorm->phalapi_curd->where('id', 1)->fetchOne();

        // 第二种，基本的CURD可以使用Model类直接完成（推荐！）
        $model = new \App\Model\Examples\CURD();
        $rs['row_2'] = $model->get(2);

        // 第三种，通过Domain领域层统一封装（强烈推荐！！）
        $domain = new DomainCURD();
        $rs['row_3'] = $domain->getList(3, 1, 5);

        // 到这一步，你可以访问当前接口（手动/通过配置开启调试模式）
        // 浏览器访问：http://localhost/phalapi/public/?s=App.Examples_CURD.SqlDebug&__debug__=1
        // 将会在debug返回字段看到SQL调试信息

        // 最后，当sys.notorm_debug和sys.enable_sql_log均开启时，将能在日志文件中纪录sql
        // 如命令：$ tail -f ./runtime/log/201905/20190523.log

        return $rs;
    }
}
