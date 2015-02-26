<?php
/**
 * 文件缓存
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-26
 */

class PhalApi_Cache_File implements PhalApi_Cache {

    protected $folder;

    protected $prefix;

    public function __construct($config) {
        $this->folder = rtrim($config['path'], '/');

        $cacheFolder = $this->createCacheFileFolder();

        if (!is_dir($cacheFolder)) {
            mkdir($cacheFolder, 0777, TRUE);
        }

        $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'phapapi';
    }

    public function set($key, $value, $expire = 600) {
        if ($key === NULL || $key === '') {
            return;
        }

        $filePath = $this->createCacheFilePath($key);

        if (!file_exists($filePath)) {
            touch($filePath);
            chmod($filePath, 0777);
        }

        $expireStr = sprintf('%010d', $expire + time());
        if (strlen($expireStr) > 10) {
            throw new PhalApi_Exception_InternalServerError(
                T('file expire is too large')
            );
        }

        file_put_contents($filePath, $expireStr . serialize($value));
    }

    public function get($key) {
        $filePath = $this->createCacheFilePath($key);

        if (file_exists($filePath)) {
            $expireTime = file_get_contents($filePath, FALSE, NULL, 0, 10);

            if ($expireTime > time()) {
                return @unserialize(file_get_contents($filePath, FALSE, NULL, 10));
            }
        }

        return NULL;
    }

    public function delete($key) {
        if ($key === NULL || $key === '') {
            return;
        }

        $filePath = $this->createCacheFilePath($key);

        @unlink($filePath);
    }

    protected function createCacheFilePath($key) {
        return $this->createCacheFileFolder() . DIRECTORY_SEPARATOR . md5($key) . '.dat';
    }

    protected function createCacheFileFolder() {
        return $this->folder . DIRECTORY_SEPARATOR . 'cache';
    }
}

