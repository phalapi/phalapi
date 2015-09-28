<?php
/**
 * 远程调度器 Task_Runner_Remote
 * 
 * - 通过远程请求接口实现任务调度
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Task_Runner_Remote extends Task_Runner {

	/**
	 * @var Task_Runner_Remote_Connector 远程接口连接器实例
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

    public function __construct(Task_MQ $mq, $step = 10, Task_Runner_Remote_Connector $contector = NULL) {
        $config = DI()->config->get('app.Task.runner.remote');

        if ($contector === NULL) {
            if (empty($config['host'])) {
                throw new PhalApi_Exception_InternalServerError(T('task miss api host for'));
            }
            $contector = new Task_Runner_Remote_Connector_Http($config);
        }

        $this->contector = $contector;
        $this->timeoutMS = isset($config['timeoutMS']) ? intval($config['timeoutMS']) : self::MAX_TIMEOUT_MS;

        parent::__construct($mq, $step);
    }

    protected function youGo($service, $params) {
        $rs = $this->contector->request($service, $params, $this->timeoutMS);

        if ($this->contector->getRet() == 404) {
            throw PhalApi_Exception_InternalServerError('task request api time out',
                array('url' => $this->contector->getUrl()));
        }

        $isOk = $this->contector->getRet() == 200 ? TRUE : FALSE;

        if (!$isOk) {
            DI()->logger->debug('task remote request not ok', 
                array('url' => $this->contector->getUrl(), 'ret' => $this->contector->getRet(), 'msg' => $this->contector->getMsg()));
        }

        return $isOk;
    }
}
