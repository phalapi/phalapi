<?php
/**
 * 接口统一入口
 * @author: dogstar 2014-10-04
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__) . '/..');

//自动加载
require_once PHALAPI_ROOT . '/Core/Loader.php';
$loader = new Core_Loader(PHALAPI_ROOT, array('Service'));

date_default_timezone_set('Asia/Shanghai');

Core_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

DI()->loader = $loader;

DI()->config = new Core_Config_File(PHALAPI_ROOT . '/Config');

DI()->request = new Core_Request();

DI()->logger = new Core_Logger_Explorer(
		Core_Logger::LOG_LEVEL_DEBUG | Core_Logger::LOG_LEVEL_INFO | Core_Logger::LOG_LEVEL_ERROR);

DI()->notorm = function() {
    $notorm = new Core_DB_NotORM(DI()->config->get('dbs'), true);
    return $notorm;
};

DI()->cache = function() {
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

//加密，测试情况下为防止本地环境没有mcrypt模块 这里作了替身
DI()->crypt = function() {
	//return new Crypt_Mock();
	return new Core_Crypt_MultiMcrypt(DI::one()->config->get('sys.crypt.mcrypt_iv'));
};

class Crypt_Mock implements Core_Crypt
{
	public function encrypt($data, $key)
	{
		echo "Crypt_Mock::encrypt($data, $key) ... \n";
		return $data;
	}
	
	public function decrypt($data, $key)
		{
		echo "Crypt_Mock::decrypt($data, $key) ... \n";
		return $data;
	}
}
