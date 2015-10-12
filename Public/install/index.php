<?php
define('PHALAPI_INSTALL', TRUE);

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

switch ($step) {
    case 2:
        include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_step2.php';
        break;
    case 3:
        if (empty($_POST['doSubmit']) || empty($_POST)) {
            header('Location: ./?step=1');
            exit(0);
        }

        //DB config
        $search = array('{project}', '{host}', '{name}', '{user}', '{password}', '{port}', '{charset}', '{prefix}', );
        $replace = array(strtolower($_POST['project']), $_POST['host'], $_POST['name'], $_POST['user'], $_POST['password'], $_POST['port'], $_POST['charset'], $_POST['prefix']);
        $configDbsContent = str_replace($search, $replace, getConfigDbsTpl());
        file_put_contents(dirname(__FILE__) . implode(DIRECTORY_SEPARATOR, array('', '..', '..', 'Config', 'dbs.php')), $configDbsContent);

        $relatePath = substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '/install/'));
        $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $relatePath . '/demo';
        include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_step3.php';
        break;
    default:
        //-1：必须但不支持 0：可选但不支持 1：完美支持
        $checkList = array(
            'php'       => array('name' => 'PHP 版本', 'status' => -1, 'tip' => '建议使用PHP 5.3.3及以上版本，否则DI无法支持匿名函数'),
            'pdo'       => array('name' => '数据库模块', 'status' => -1, 'tip' => '建议使用PDO扩展，否则NotORM无法使用PDO进行数据库操作'),
            'memcache'  => array('name' => 'Memcache扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用Memcache缓存'),
            'mcrypt'    => array('name' => 'Mcrypt扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用mcrypt进行加密处理'),
            'runtime'   => array('name' => '目录权限', 'status' => -1, 'tip' => '日志目录若缺少写入权限，则不能写入日记和进行文件缓存'),
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
        $runtimePath = file_exists($runtimePath) ? realpath($runtimePath) : $runtimePath;
        $checkList['runtime']['tip'] = $runtimePath . '<br>' . $checkList['runtime']['tip'];
        if (is_writeable($runtimePath)) {
            $checkList['runtime']['status'] =  1;
        }

        include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_step1.php';
}


function getConfigDbsTpl() {
$configDbs = <<<EOT
<?php
/**
 * 分库分表的自定义数据库路由配置
 */

return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'db_{project}' => array(                         //服务器标记
            'host'      => '{host}',             //数据库域名
            'name'      => '{name}',               //数据库名字
            'user'      => '{user}',                  //数据库用户名
            'password'  => '{password}',	                    //数据库密码
            'port'      => '{port}',                  //数据库端口
            'charset'   => '{charset}',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => '{prefix}',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_{project}'),
            ),
        ),

        /**
        'demo' => array(                                                //表名
            'prefix' => '{prefix}',                                         //表名前缀
            'key' => 'id',                                              //表主键名
            'map' => array(                                             //表路由配置
                array('db' => 'db_{project}'),                               //单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_{project}'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),
         */
    ),
);

EOT;

return $configDbs;
}


function getInitTpl() {
$initTpl = <<<EOT
<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT);

/** ---------------- 注册&初始化服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

//日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', 
    PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM，$_GET['__sql__']可自行改名
DI()->notorm = function() {
    $debug = !empty($_GET['__sql__']) ? true : false;
    return new PhalApi_DB_NotORM(DI()->config->get('dbs'), $debug);
};

//调试模式，$_GET['__debug__']可自行改名
DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');

//翻译语言包设定
SL('{language}');

/** ---------------- 以下服务组件就根据需要定制注册 ---------------- **/

//签名验证服务
DI()->filter = 'Common_SignFilter';

/**
//缓存 - Memcached
DI()->cache = function() {
    //根据Memcached是否加载
    if(extension_loaded('memcached')){
        $mc = new PhalApi_Cache_Memcached(DI()->config->get('sys.mc'));
    }else{
        $mc = new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
    }
	return $mc;
};
 */

/**
//支持JsonP的返回
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */

EOT;

return $initTpl;
}
