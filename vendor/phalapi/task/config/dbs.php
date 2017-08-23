<?php
/**
 * Task计划任务配置
 *
 * TODO: 请将以下配置拷贝到 ./config/dbs.php 文件对应的位置中，未配置的表将使用默认路由
 */

return array(

    'tables' => array(
        // 10张表，可根据需要，自行调整表前缀、主键名和路由
        'task_mq' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
                array('start' => 0, 'end' => 9, 'db' => 'db_master'),
            ),
        ),
    )
);


