<?php
namespace PhalApi\Task\Progress;

/**
 * 触发器接口
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150520
 */

interface Trigger {

	/**
	 * 进程的具体操作
	 * @param string $params 对应数据库表task_progress.fire_params字段
	 */
    public function fire($params);
}
