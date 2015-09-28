<?php
/**
 * MQ队列接口
 * 
 * - 单个添加，批量弹出
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

interface Task_MQ {

    /**
     * 单个添加
     * @param string $service 接口服务名称，如：Default.Index
     * @param array $params 接口服务参数
     */
    public function add($service, $params = array());

    /**
     * 批量弹出
     * @param string $service 需要获取的接口服务名称
     * @param int $num 弹出的个数
     */
    public function pop($service, $num = 1);
}
