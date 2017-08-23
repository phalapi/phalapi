<?php
namespace PhalApi\Task\Model;

use PhalApi\Model\NotORMModel as NotORM;

class TaskProgress extends NotORM {

    const STATE_WAITTING = 0;
    const STATE_RUNNING = 1;
    const STATE_EXCEPTION = -1;

    const ENABLE_ON = 1;
    const ENABLE_OFF = 0;

    protected function getTableName($id = NULL) {
        return 'task_progress';
    }

    public function getWrongItems($maxLastFireTime) {
        $rows = $this->getORM()
            ->select('id, title')
            ->where('state != ?', self::STATE_WAITTING)
            ->where('last_fire_time < ?', $maxLastFireTime)
            ->where('enable = ?', self::ENABLE_ON)
            ->order('last_fire_time ASC')
            ->fetchAll();

        return !empty($rows) ? $rows : array();
    }

    public function resetWrongItems($id) {
        return $this->update($id, array('state' => self::STATE_WAITTING));
    }

    public function getAllWaittingItems() {
        $rows = $this->getORM()
            ->select('id, title, trigger_class, fire_params')
            ->where('state', self::STATE_WAITTING)
            ->where('interval_time + last_fire_time < ?', $_SERVER['REQUEST_TIME'])
            ->where('enable = ?', self::ENABLE_ON)
            ->fetchAll();

        return !empty($rows) ? $rows : array();
    }

    public function isRunnable($id) {
        $row = $this->get($id, 'enable, state');

        if (empty($row)) {
            return FALSE;
        }

        return ($row['state'] == self::STATE_WAITTING && $row['enable'] == self::ENABLE_ON) ? TRUE : FALSE;
    }

    public function setRunningState($id) {
        return $this->update($id, array('state' => self::STATE_RUNNING));
    }

    public function updateFinishItem($id, $result) {
        $data = array(
            'result' => json_encode($result),
            'state' => self::STATE_WAITTING,
            'last_fire_time' => $_SERVER['REQUEST_TIME'],
        );

        return $this->update($id, $data);
    }

    public function updateExceptionItem($id, $errorMsg) {
        $data = array(
            'result' => $errorMsg,
            'state' => self::STATE_EXCEPTION,
            'last_fire_time' => $_SERVER['REQUEST_TIME'],
        );

        return $this->update($id, $data);
    }
}
