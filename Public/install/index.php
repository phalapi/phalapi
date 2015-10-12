<?php
define('PHALAPI_INSTALL', TRUE);

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

switch ($step) {
    case 2:
        include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_step2.php';
        break;
    default:
        //-1：必须但不支持 0：可选但不支持 1：完美支持
        $checkList = array(
            'php' => array('name' => 'PHP 版本', 'status' => -1, 'tip' => '建议使用PHP 5.3.3及以上版本，否则DI无法支持匿名函数'),
            'pdo' => array('name' => '数据库模块', 'status' => -1, 'tip' => '建议使用PDO扩展，否则NotORM无法使用PDO进行数据库操作'),
            'memcache' => array('name' => 'Memcache扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用PhalApi_Cache_Memcache'),
            'mcrypt' => array('name' => 'Mcrypt扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用mcrypt进行加密处理'),
            'runtime' => array('name' => '目录权限', 'status' => -1, 'tip' => '日志目录若缺少写入权限，则不能写入日记和进行文件缓存'),
        );

        if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
            $checkList['php']['status'] = 1;
        }
        if (class_exists('PDO', false) && extension_loaded('PDO')) {
            $checkList['pdo']['status'] = 1;
        }
        if (class_exists('Memcache', false) && extension_loaded('memcache')) {
            $checkList['memcache']['status'] = 1;
        }
        /**
        if (class_exists('Memcached', false) && extension_loaded('memcached')) {
            $checkList['memcached']['status'] = 1;
        }
         */
        if (extension_loaded('mcrypt')) {
            $checkList['mcrypt']['status'] = 1;
        }
        $runtimePath = dirname(__FILE__) . implode(DIRECTORY_SEPARATOR, array('', '..', '..', 'Runtime'));
        $runtimePath = realpath($runtimePath);
        $checkList['runtime']['tip'] = $runtimePath . '<br>' . $checkList['runtime']['tip'];
        if (file_exists($runtimePath) && is_writeable($runtimePath)) {
            $checkList['runtime']['status'] =  1;
        }

        include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_step1.php';
}
