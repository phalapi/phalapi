<?php
namespace Portal\Api\User;

use Portal\Common\DataApi as Api;
use Portal\Model\User\User as UserModel;

/**
 * 用户插件
 * @author dogstar 20200331
 *
 */
class User extends Api {
    
    public function getRules() {
        $rules = parent::getRules();
        $rules['getUserRegisterStat'] = array(
            'days' => array('name' => 'days', 'default' => 30, 'type' => 'int', 'desc' => '统计天数'),
        );
        return $rules;
    }
    
    protected function getDataModel() {
        return new UserModel();
    }
    
    // 列表返回的字段
    protected function getTableListSelect() {
        return 'id,username,nickname,reg_time,avatar,mobile,sex,email';
    }
    
    // 取到列表数据后的加工处理
    protected function afterTableList($items) {
        foreach ($items as &$itRef) {
            $itRef['reg_time'] = date('Y-m-d', $itRef['reg_time']);
        }
        return $items;
    }
    
    // 获取单个数据时需要返回的字段
    protected function getDataSelect() {
        return 'id,username,nickname,reg_time,avatar,mobile,sex,email';
    }
    
    /**
     * 用户注册统计
     * @desc 获取最近用户注册的报表统计数据
     */
    public function getUserRegisterStat() {
        $days = $this->days;
        $start_reg_time = strtotime(date('Y-m-d 00:00:00', strtotime("-{$days} days")));
        $now_time = time();
        $model = new UserModel;
        $stat = $model->getUserRegisterStat($start_reg_time);
        
        // 填充默认值
        $statMap = array();
        for ($t = $start_reg_time; $t < $now_time; $t += 86400) {
            $d = date('Y-m-d', $t);
            $statMap[$d] = 0;
        }
        
        foreach ($stat as $it) {
            $statMap[$it['reg_date']] = $it['reg_total'];
        }
        
        $xAxisData = array_keys($statMap);
        $seriesData = array_values($statMap);
        
        
        return array('xAxisData' => $xAxisData, 'seriesData' => $seriesData);
    }
}