<?php
namespace PhalApi\Task\MQ;

use PhalApi\Task\MQ\KeyValueMQ;
use PhalApi\Cache\FileCache;

/**
 * 文件MQ
 *
 * - 队列存放于本地文件 中，不支持分布式MQ
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class FileMQ extends KeyValueMQ {

    public function __construct(PhalApi_Cache_File $fileCache = NULL) {
        if ($fileCache === NULL) {
            $config = \PhalApi\DI()->config->get('app.Task.mq.file');
            if (!isset($config['path'])) {
                $config['path'] = API_ROOT . '/Runtime';
            }
            if (!isset($config['prefix'])) {
                $config['prefix'] = 'phalapi_task';
            }

            $fileCache = new FileCache($config);
        }

        parent::__construct($fileCache);
    }
}
