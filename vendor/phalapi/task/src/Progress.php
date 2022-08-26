<?php
namespace PhalApi\Task;

use PhalApi\Task\Model\TaskProgress;

/**
 * 计划任务进程类
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150520 - 20220826
 */

class Progress {

    /**
     * @var int MAX_LAST_FIRE_TIME_INTERVAL 修复的最大时间间隔
     */
    const MAX_LAST_FIRE_TIME_INTERVAL = 600;

    /**
     * @var PhalApi\Task\Model\TaskProgress 对数据库的操作
     */
    protected $model;

    public function __construct() {
        $this->model = new TaskProgress();
    }

    /**
     * 进行进程调度
     * 
     * - 1、尝试修复异常的任务
     * - 2、执行全部空闲的任务
     */
    public function run() {
        $this->tryToResetWrongItems();

        $this->runAllWaittingItems();

        return TRUE;
    }

    protected function tryToResetWrongItems() {
        $maxLastFireTime = time() - self::MAX_LAST_FIRE_TIME_INTERVAL;

        $wrongItems = $this->model->getWrongItems($maxLastFireTime);

        foreach ($wrongItems as $item) {
            $this->model->resetWrongItems($item['id']);

            \PhalApi\DI()->logger->debug('已修复异常的计划任务', $item);
        }
    }

    protected function runAllWaittingItems() {
        $waittingItems = $this->model->getAllWaittingItems();

        foreach ($waittingItems as $item) {
            //
            if (!$this->model->isRunnable($item['id'])) {
                continue;
            }

            $class = !empty($item['trigger_class']) ? $item['trigger_class'] : 'PhalApi\Task\Progress\Trigger\CommonTrigger';
            $params = $item['fire_params'];

            if (empty($class) || !class_exists($class)) {
                \PhalApi\DI()->logger->error('错误：trigger_class非法', $item);
                $this->model->updateExceptionItem($item['id'], '无法执行计划任务，trigger_class非法');
                continue;
            }

            $trigger = new $class();
            if (!is_callable(array($class, 'fire'))) {
                \PhalApi\DI()->logger->error('错误：无法执行fire()方法', $item);
                $this->model->updateExceptionItem($item['id'], '无法执行计划任务，无法执行fire()方法');
                continue;
            }

            $this->model->setRunningState($item['id']);

            try {
                // 避免本地执行接口返回过长的调试信息，关闭调试
                $beforeDebug = \PhalApi\DI()->debug;
                \PhalApi\DI()->debug = false;
                $result = call_user_func(array($trigger, 'fire'), $params);

                // 恢复调试
                \PhalApi\DI()->debug = $beforeDebug;

                $this->model->updateFinishItem($item['id'], $result);
            } catch (\Exception $ex) {
                $this->model->updateExceptionItem($item['id'], $ex->getMessage());
                throw $ex;
            }
        }
    }
}
