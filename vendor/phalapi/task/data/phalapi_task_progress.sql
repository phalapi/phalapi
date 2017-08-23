CREATE TABLE `tbl_task_progress` (
      `id` bigint(20) NOT NULL AUTO_INCREMENT,
      `title` varchar(200) DEFAULT '' COMMENT '任务标题',
      `trigger_class` varchar(50) DEFAULT '' COMMENT '触发器类名',
      `fire_params` varchar(255) DEFAULT '' COMMENT '需要传递的参数，格式自定',
      `interval_time` int(11) DEFAULT '0' COMMENT '执行间隔，单位：秒',
      `enable` tinyint(1) DEFAULT '1' COMMENT '是否启动，1启动，0禁止',
      `result` varchar(255) DEFAULT '' COMMENT '运行的结果，以json格式保存',
      `state` tinyint(1) DEFAULT '0' COMMENT '进程状态，0空闲，1运行中，-1异常退出',
      `last_fire_time` int(11) DEFAULT '0' COMMENT '上一次运行时间',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

