<?php
namespace PhalApi\Task;

use PhalApi\Task\MQ;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 计划任务客户端类 PhalApi\Task\Lite
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Lite {

    /**
     * PhalApi\Task\MQ $mq MQ队列
     */
    protected $mq;

    public function __construct(MQ $mq) {
        $this->mq = $mq;
    }

    /**
     * 添加一个计划任务到MQ队列
     * @param string $service 接口服务名称，如：Site.Index
     * @param array $params 接口服务参数
     */
    public function add($service, $params = array()) {
        if (empty($service) || count(explode('.', $service)) < 2) {
            return FALSE;
        }
        if (!is_array($params)) {
            return FALSE;
        }

        $rs = $this->mq->add($service, $params);

        if (!$rs) {
            \PhalApi\DI()->logger->debug('task add a new mq', 
                array('service' => $service, 'params' => $params));

            return FALSE;
        }

        return TRUE;
    }
}
