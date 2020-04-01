<?php
namespace Portal\Model\User;

use PhalApi\Model\DataModel;

class User extends DataModel {
    public function getTableName($id) {
        return 'phalapi_user';
    }
    
    /**
     * 用户注册统计
     * @desc 获取最近用户注册的报表统计数据
     */
    public function getUserRegisterStat($start_reg_time) {
        return $this->getORM()
            ->select("count(*) as reg_total, FROM_UNIXTIME(reg_time, '%Y-%m-%d') as reg_date")
            ->where('reg_time > ?', $start_reg_time)
            ->group("FROM_UNIXTIME(reg_time, '%Y-%m-%d')")
            ->order('reg_date')
            ->fetchAll();
    }
}
