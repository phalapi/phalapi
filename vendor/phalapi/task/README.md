# task
PhalApi 2.x 扩展类库 - Task计划任务，以接口服务形式实现的新型计划任务。

## 简单使用说明

### 安装
修改你的项目根目录下的 composer.json文件，并添加：  
```
"require": {
        "phalapi/task": "dev-master"
},
```

然后，执行：composer update，即可安装。  

### 配置

修改 ./config/app.php文件，并添加：  
```php
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
        ),
    ),
```

修改DI文件，并注册Task服务，即在 ./config/di.php 添加：  
```
$mq = new \PhalApi\Task\MQ\FileMQ();  //可以选择你需要的MQ
$di->taskLite = new \PhalApi\Task\Lite($mq);
```

然后，创建以下数据库表（注意同步修改表前缀）：  

```sql
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

```

并在上面的计划任务表中，添加你需要的计划任务，例如：  

 + title 任务标题
 + trigger_class 触发器类名，默认是PhalApi\Task\Progress\Trigger\CommonTrigger
 + fire_params 需要传递的参数，默认第一个是待执行的接口服务名称，例如：App.Site.Index，第二是MQ的类名，默认是：PhalApi\Task\MQ\FileMQ，第三个是调试器类名，默认是：PhalApi\Task\Runner\LocalRunner。这三个参数使用&分割
 + interval_time 执行间隔，单位：秒
 + enable 是否启动，1启动，0禁止

## 启动计划任务

在启动计划任务前，我们需要编写简单的脚本，一如这样：  
```php
// my_task.php
<?php
/**
 * 计划任务入口示例
 */
require_once dirname(__FILE__) . '/../public/init.php';

try {
    $progress = new \PhalApi\Task\Progress();
    $progress->run();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}
```

最后，就可以直接在命令行，或者通过crontab定时执行上面的计划任务啦～～

## 文档  

更多详细资料请参考：  

 + [新型计划任务：以接口形式实现的计划任务](https://www.phalapi.net/wikis/1-31.html)  
 + [演进：新型计划任务续篇](https://www.phalapi.net/wikis/2-15.html)  
 + [扩展类库：新型计划任务](https://www.phalapi.net/wikis/3-6.html)  

