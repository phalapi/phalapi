<?php
/**
 * 文件MQ
 *
 * - 队列存放于本地文件 中，不支持分布式MQ
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Task_MQ_File implements Task_MQ {

    /**
     * 最大缓存时间，一年
     */
    const MAX_EXPIRE_TIME = 31536000;

    /**
     * @var PhalApi_Cache_File $fileCache 文件缓存实例
     */
    protected $fileCache;

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

        $this->fileCache = $fileCache;
    }

    public function add($service, $params = array()) {
        $list = $this->fileCache->get($service);
        if (empty($list)) {
            $list = array();
        }

        $list[] = $params;

        $this->fileCache->set($service, $list, self::MAX_EXPIRE_TIME);

        return true;
    }

    public function pop($service, $num = 1) {
        $rs = array();
        if ($num <= 0) {
            return $rs;
        }

        $list = $this->fileCache->get($service);
        if (empty($list)) {
            $list = array();
        }

        $rs = array_splice($list, 0, $num);

        $this->fileCache->set($service, $list, self::MAX_EXPIRE_TIME);

        return $rs;
    }
}
