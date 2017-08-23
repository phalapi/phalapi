<?php
namespace PhalApi\Task\Runner;

use PhalApi\Task\Runner;
use PhalApi\Task\MQ;
use PhalApi\Task\Runner\Remote\Connector;
use PhalApi\Task\Runner\Remote\Connector\HttpConnector;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 远程调度器 RemoteRunner
 * 
 * - 通过远程请求接口实现任务调度
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class RemoteRunner extends Runner {

	/**
	 * @var PhalApi\Task\Runner\Remote\Connector 远程接口连接器实例
	 */
    protected $contector;

    /**
     * @var int $timeoutMS 接口超时（单位：毫秒）
     */
    protected $timeoutMS;

    /**
     * @var int 默认最大接口超时
     */
    const MAX_TIMEOUT_MS = 3000;

    public function __construct(MQ $mq, $step = 10, Connector $contector = NULL) {
        $config = \PhalApi\DI()->config->get('app.Task.runner.remote');

        if ($contector === NULL) {
            if (empty($config['host'])) {
                throw new InternalServerErrorException(\PhalApi\T('task miss api host for'));
            }
            $contector = new HttpConnector($config);
        }

        $this->contector = $contector;
        $this->timeoutMS = isset($config['timeoutMS']) ? intval($config['timeoutMS']) : self::MAX_TIMEOUT_MS;

        parent::__construct($mq, $step);
    }

    protected function youGo($service, $params) {
        $rs = $this->contector->request($service, $params, $this->timeoutMS);

        if ($this->contector->getRet() == 404) {
            throw new InternalServerErrorException(\PhalApi\T('task request api time out',
                array('url' => $this->contector->getUrl())));
        }

        $isOk = $this->contector->getRet() == 200 ? TRUE : FALSE;

        if (!$isOk) {
            \PhalApi\DI()->logger->debug('task remote request not ok', 
                array('url' => $this->contector->getUrl(), 'ret' => $this->contector->getRet(), 'msg' => $this->contector->getMsg()));
        }

        return $isOk;
    }
}
