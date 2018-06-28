<?php
namespace PhalApi\Task;

use PhalApi\Task\Model\TaskProgress;

/**
 * 计划任务进程类
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150520
 */

class Progress {

    /**
     * @var int MAX_LAST_FIRE_TIME_INTERVAL 修复的最大时间间隔
     */
    const MAX_LAST_FIRE_TIME_INTERVAL = 86400;

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
        $maxLastFireTime = $_SERVER['REQUEST_TIME'] - self::MAX_LAST_FIRE_TIME_INTERVAL;

        $wrongItems = $this->model->getWrongItems($maxLastFireTime);

        foreach ($wrongItems as $item) {
            $this->model->resetWrongItems($item['id']);

            \PhalApi\DI()->logger->debug('task try to reset wrong items', $item);
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
                \PhalApi\DI()->logger->error('Error: task can not run illegal class', $item);
                $this->model->updateExceptionItem($item['id'], 'task can not run illegal class');
                continue;
            }

            $trigger = new $class();
            if (!is_callable(array($class, 'fire'))) {
                \PhalApi\DI()->logger->error('Error: task can not call fire()', $item);
                $this->model->updateExceptionItem($item['id'], 'task can not call fire()');
                continue;
            }

            $this->model->setRunningState($item['id']);

            try {
                $result = call_user_func(array($trigger, 'fire'), $params);

                $this->model->updateFinishItem($item['id'], $result);
            } catch (Exception $ex) {
                throw $ex;
                $this->model->updateExceptionItem($item['id'], $ex->getMessage());
            }
        }
    }
}
