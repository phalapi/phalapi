<?php
/**
 * 数据库MQ
 * 
 * - 队列存放于数据库表中，并支持分表
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Model_Task_TaskMq extends PhalApi_Model_NotORM {

    protected function getTableName($id = NULL) {
        $prefix = hexdec(substr(sha1($id), -1)) % 10;
        return 'task_mq_' . $prefix;
    }

    public function add($service, $params = array()) {
        $data = array(
            'service' => $service,
            'params' => json_encode($params),
            'create_time' => time(),
        );

        $id = $this->insert($data, $service);

        return $id > 0 ? TRUE : FALSE;
    }

    public function pop($service, $num = 1) {
        $rows = $this->getORM($service)
            ->select('id, params')
            ->where('service', $service)
            ->order('id ASC')
            ->limit(0, $num)
            ->fetchAll();

        if (empty($rows)) {
            return array();
        }

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row['id'];
        }

        $this->getORM($service)->where('id', $ids)->delete();

        $rs = array();
        foreach ($rows as $row) {
            $params = json_decode($row['params'], TRUE);
            if (!is_array($params)) {
                $params = array();
            }

            $rs[] = $params;
        }

        return $rs;
    }
}
