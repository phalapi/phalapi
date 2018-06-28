<?php

return array(

    //请将以下配置拷贝到 ./Config/app.php 文件中

    /**
     * 计划任务配置
     */
    'Task' => array(
        //MQ队列设置，可根据使用需要配置
        'mq' => array(
            // 默认使用文件MQ
            'file' => array(
                'path' => API_ROOT . '/runtime',
                'prefix' => 'phalapi_task',
            ),
            // 也可以根据需要，切换使用Redis MQ
            'redis' => array(
                'host' => '127.0.0.1',
            	'port' => 6379,
                'prefix' => 'phalapi_task',
                'auth' => '',
            ),
        ),

        //Runner设置，如果使用远程调度方式，请加此配置
        'runner' => array(
            'remote' => array(
                'host' => 'http://library.phalapi.net/demo/',
                'timeoutMS' => 3000,
            ),
        ),
    ),
);
