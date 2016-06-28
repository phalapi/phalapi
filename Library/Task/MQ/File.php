<?php
/**
 * 文件MQ
 *
 * - 队列存放于本地文件 中，不支持分布式MQ
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Task_MQ_File extends Task_MQ_KeyValue {

    public function __construct(PhalApi_Cache_File $fileCache = NULL) {
        if ($fileCache === NULL) {
            $config = DI()->config->get('app.Task.mq.file');
            if (!isset($config['path'])) {
                $config['path'] = API_ROOT . '/Runtime';
            }
            if (!isset($config['prefix'])) {
                $config['prefix'] = 'phalapi_task';
            }

            $fileCache = new PhalApi_Cache_File($config);
        }

        parent::__construct($fileCache);
    }
}
