<?php
namespace PhalApi\Task\MQ;

use PhalApi\Task\MQ;
use PhalApi\Cache;
use PhalApi\Cache\RedisCache;

class RedisMQ implements MQ {

    protected $redisCache;

    public function __construct(RedisCache $redisCache= NULL) {
        if ($redisCache === NULL) {
            $config = \PhalApi\DI()->config->get('app.Task.mq.redis');

            if (!isset($config['host'])) {
                $config['host'] = '127.0.0.1';
            }
            if (!isset($config['port'])) {
                $config['port'] = 6379;
            }
            if (!isset($config['prefix'])) {
                $config['prefix'] = 'phalapi_task';
            }

            $redisCache = new RedisCache($config);
        }

        $this->redisCache = $redisCache;
    }

    public function add($service, $params = array()) {
        $num = $this->redisCache->rPush($service, $params);

        return $num > 0 ? TRUE : FALSE;
    }

    public function pop($service, $num = 1) {
        $rs = array();

        while($num > 0) {
            $params = $this->redisCache->lPop($service);

            if ($params === NULL) {
                break;
            }

            $rs[] = $params;

            $num--;
        }

        return $rs;
    }
}
