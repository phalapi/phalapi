<?php
namespace PhalApi\Tests;

use PhalApi\Loader;
use PhalApi\Config\FileConfig;
use PhalApi\Logger\ExplorerLogger;
use PhalApi\Filter;
use PhalApi\Logger;
use PhalApi\Api;
use PhalApi\Crypt;
use PhalApi\Exception\BadRequestException;
use PhalApi\Response\JsonResponse;
use PhalApi\Response\JsonpResponse;
use PhalApi\Database\NotORMDatabase;

/**
 * 接口统一入口
 * @author: dogstar 2014-10-04
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__));

require API_ROOT . '/../vendor/autoload.php';

$loader = new Loader(API_ROOT);

$di = \PhalApi\DI();

date_default_timezone_set('Asia/Shanghai');

\PhalApi\SL('zh_cn');

/** ---------------- 注册&初始化服务组件 ---------------- **/

$di->loader = $loader;

$di->config = new FileConfig(dirname(__FILE__) . '/config');

class ExplorerLoggerTest extends ExplorerLogger {

    public function log($type, $msg, $data) {
        if (empty($_ENV['silence'])) {
            parent::log($type, $msg, $data);
        }
    }
}

$di->logger = new ExplorerLoggerTest(
		Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

$di->debug = true;

$di->notorm = function() {
    $notorm = new NotORMDatabase(\PhalApi\DI()->config->get('dbs'), true);
    return $notorm;
};

$di->cache = function() {
    //$mc = new PhalApi_Cache_Memcached(\PhalApi\DI()->config->get('sys.mc'));
    $mc = new MemcachedMock();
	return $mc;
};

class MemcachedMock {
    public $data = array();

    public function __call($method, $params)
    {
        if (empty($_ENV['silence'])) {
            echo 'Memcached::' . $method . '() with: ', json_encode($params), " ... \n";
        }
    }

    public function get($key)
    {
        if (empty($_ENV['silence'])) {
            echo "Memcached::get($key) ... \n";
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value, $expire)
    {
        if (empty($_ENV['silence'])) {
            echo "Memcached::get($key, ", json_encode($value), ", $expire) ... \n";
        }
        $this->data[$key] = $value;
    }

    public function delete($key)
    {
        unset($this->data[$key]);
    }
}

//加密，测试情况下为防止本地环境没有mcrypt模块 这里作了替身
$di->crypt = function() {
	//return new MockCrypt();
	// TODO return new PhalApi_Crypt_MultiMcrypt(\PhalApi\DI()->config->get('sys.crypt.mcrypt_iv'));
};

class MockCrypt implements Crypt
{
	public function encrypt($data, $key)
    {
        if (empty($_ENV['silence'])) {
            echo "Crypt_Mock::encrypt($data, $key) ... \n";
        }
		return $data;
	}
	
	public function decrypt($data, $key)
    {
        if (empty($_ENV['silence'])) {
            echo "Crypt_Mock::decrypt($data, $key) ... \n";
        }
		return $data;
	}
}

/** ---------------- 公共的测试替身或桩 ---------------- **/

class JsonResponseMock extends JsonResponse {

    protected function handleHeaders($headers) {
    }
}

class JsonpResponseMock extends JsonpResponse {

    protected function handleHeaders($headers) {
    }
}

class ImplApi extends Api {

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

    public function getTime()
    {
        return time();
    }
}

class ImplFilter implements Filter {

    public function check() {

    }
}

class ImplExceptionFilter implements Filter {

    public function check() {
        throw new BadRequestException('just for test');
    }
}
