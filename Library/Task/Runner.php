<?php
/**
 * 计划任务调度器
 * 
 * - 异常失败，会轮循重试
 * - 彩蛋式的抽象方法名
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

abstract class Task_Runner {

    /**
     * @var MQ队列实例
     */
    protected $mq;

    /**
     * @var int $step 批次的数据，步长
     */
    protected $step;

    /**
     * @param Task_MQ $mq MQ队列实例
     * @param int $step 批次的数据，步长
     */
    public function __construct(Task_MQ $mq, $step = 10) {
        $this->mq = $mq;

        $this->step = max(1, intval($step));
    }

    /**
     * 执行任务
     * @param string $service MQ中的接口服务名称，如：Default.Index
     * @return array('total' => 总数量, 'fail' => 失败数量)
     */
    public function go($service) {
        $rs = array('total' => 0, 'fail' => 0);

        $todoList = $this->mq->pop($service, $this->step);
        $failList = array();

        while (!empty($todoList)) {
            $rs['total'] += count($todoList);

            foreach ($todoList as $params) {
                try {
                    $isFinish = $this->youGo($service, $params);

                    if (!$isFinish) {
                        $rs['fail'] ++;
                    }
                } catch (PhalApi_Exception_InternalServerError $ex) {
                    $rs['fail'] ++;

                    $failList[] = $params;

                    DI()->logger->error('task occur exception to go',
                        array('service' => $service, 'params' => $params, 'error' => $ex->getMessage()));
                }
            }

            $todoList = $this->mq->pop($service, $this->step);
        }

        foreach ($failList as $params) {
            $this->mq->add($service, $params);
        }

        return $rs;
    }

    /**
     * 具体的执行，这里使用了一个彩蛋的命名
     * @param string $service MQ中的接口服务名称，如：Default.Index
     * @param array $params 参数
     * @return boolean 成功返回TRUE，失败返回FALSE
     */
    abstract protected function youGo($service, $params);
}
