<?php
namespace PhalApi\Task\Progress\Trigger;

use PhalApi\Task\Progress\Trigger;

/**
 * 通用 触发器接口
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150520
 */

class CommonTrigger implements Trigger {

    public function fire($params) {
        $paramsArr = explode('&', $params);

        $service = !empty($paramsArr[0]) ? trim($paramsArr[0]) : '';
        $mqClass = !empty($paramsArr[1]) ? trim($paramsArr[1]) : 'PhalApi\Task\MQ\FileMQ';
        $runnerClass = !empty($paramsArr[2]) ? trim($paramsArr[2]) : 'PhalApi\Task\Runner\LocalRunner';

        $mq = new $mqClass();
        $runner = new $runnerClass($mq);

        return $runner->go($service);
    }
}
