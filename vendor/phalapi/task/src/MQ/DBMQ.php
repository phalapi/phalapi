<?php
namespace PhalApi\Task\MQ;

use PhalApi\Task\MQ;
use PhalApi\Task\Model\TaskMq;

/**
 * 数据库MQ
 * 
 * - 队列存放于数据库表中，并支持分表
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class DBMQ implements MQ {

    public function add($service, $params = array()) {
        $model = new TaskMq();
        return $model->add($service, $params);
    }

    public function pop($service, $num = 1) {
        $model = new TaskMq();
        return $model->pop($service, $num);
    }
}
