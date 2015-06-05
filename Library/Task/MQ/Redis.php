<?php

class Task_MQ_Redis implements Task_MQ {

    protected $redisCache;

    public function __construct(PhalApi_Cache_Redis $redisCache= NULL) {
        if ($redisCache === NULL) {
            $config = DI()->config->get('app.Task.mq.redis');

            if (!isset($config['host'])) {
                $config['host'] = '127.0.0.1';
            }
            if (!isset($config['port'])) {
                $config['port'] = 6379;
            }
            if (!isset($config['prefix'])) {
                $config['prefix'] = 'phalapi_task';
            }

            $redisCache = new PhalApi_Cache_Redis($config);
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
