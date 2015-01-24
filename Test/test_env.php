<?php
/**
 * 接口统一入口
 * @author: dogstar 2014-10-04
 */
 
defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__) . '/..');

//自动加载
require_once PHALAPI_ROOT . '/Core/Loader.php';
$loader = new Core_Loader(PHALAPI_ROOT, array('Service'));

//注册&初始化服务组件: 依赖注入、使用和创建分离
Core_DI::one()->loader = $loader;

Core_DI::one()->config = new Core_Config_File(PHALAPI_ROOT . '/Config');

Core_DI::one()->request = new Core_Request();

Core_DI::one()->logger = new Core_Logger_Explorer(
		Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

Core_DI::one()->notorm = function() {
    $notorm = new Core_DB_NotORM(Core_DI::one()->config->get('dbs'), true);
    return $notorm;
};

Core_DI::one()->cache = function() {
    //$mc = new Core_Cache_Memecahced(Core_DI::one()->config->get('sys.memcached'));
    $mc = new Memcached_Mock();
	return $mc;
};

class Memcached_Mock {
    public $data = array();

    public function __call($method, $params)
    {
        echo 'Memcached::' . $method . '() with: ', json_encode($params), " ... \n";
    }

    public function get($key)
    {
        echo "Memcached::get($key) ... \n";
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value, $expire)
    {
        echo "Memcached::get($key, ", json_encode($value), ", $expire) ... \n";
        $this->data[$key] = $value;
    }

    public function delete($key)
    {
        unset($this->data[$key]);
    }
}

Core_DI::one()->loader->loadFile('PhalApi.php');

date_default_timezone_set('Asia/Shanghai');

Core_Translator::setLanguage('zh_cn');
