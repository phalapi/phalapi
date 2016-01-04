<?php
/**
 * 接口统一入口
 * @author: dogstar 2014-10-04
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__));

//自动加载
require_once API_ROOT . '/../PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT, array('Service'));

date_default_timezone_set('Asia/Shanghai');

PhalApi_Translator::setLanguage('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

DI()->loader = $loader;

DI()->config = new PhalApi_Config_File(dirname(__FILE__) . '/Config');

DI()->request = new PhalApi_Request();

DI()->logger = new PhalApi_Logger_Explorer(
		PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

DI()->notorm = function() {
    $notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), true);
    return $notorm;
};

DI()->cache = function() {
    //$mc = new PhalApi_Cache_Memcached(DI()->config->get('sys.mc'));
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

if (!class_exists('Memcached')) {
    class Memcached extends Memcached_Mock {
    }
}

if (!class_exists('Redis')) {

    class Redis {

        public function __call($method, $params) {
            echo 'Redis::' . $method . '() with: ', json_encode($params), " ... \n";
        }

    }
}

//加密，测试情况下为防止本地环境没有mcrypt模块 这里作了替身
DI()->crypt = function() {
	//return new Crypt_Mock();
	return new PhalApi_Crypt_MultiMcrypt(DI()->config->get('sys.crypt.mcrypt_iv'));
};

class Crypt_Mock implements PhalApi_Crypt
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

/** ---------------- 公共的测试替身或桩 ---------------- **/

class PhalApi_Response_Json_Mock extends PhalApi_Response_Json {

    protected function handleHeaders($headers) {
    }
}

class PhalApi_Response_JsonP_Mock extends PhalApi_Response_JsonP {

    protected function handleHeaders($headers) {
    }
}

class PhalApi_Api_Impl extends PhalApi_Api {

    public function getRules() {
        return array(
            '*' => array( 
                'version' => array('name' => 'version'),
            ),
            'add' => array(
                'left' => array('name' => 'left', 'type' => 'int'),
                'right' => array('name' => 'right', 'type' => 'int'),
            ),
        );
    }

    public function add()
    {
        return $this->left + $this->right;
    }
}

class PhalApi_Filter_Impl implements PhalApi_Filter {

    public function check() {

    }
}

if (!class_exists('Yaconf', false)) {
    class Yaconf {
        public static function __callStatic($method, $params) {
            echo "Yaconf::$method()...\n";
        }
    }
}
