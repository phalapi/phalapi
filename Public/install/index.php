<?php
define('PHALAPI_INSTALL', TRUE);
define('D_S', DIRECTORY_SEPARATOR);

$step = isset($_GET['step']) ? intval($_GET['step']) : 0;

switch ($step) {
    //第一步：环境检测
case 1:
    if (file_exists('_install.lock')) {
        $error = '项目已安装，请不要重复安装，并建议手动删除 ./install 此目录以及目录下的全部文件';
        include dirname(__FILE__) . D_S . '_error.php';
        exit(0);
    }
    //-1：必须但不支持 0：可选但不支持 1：完美支持
    $checkList = array(
        'php'       => array('name' => 'PHP 版本', 'status' => -1, 'tip' => '建议使用PHP 5.3.3及以上版本，否则DI无法支持匿名函数'),
        'pdo'       => array('name' => '数据库模块', 'status' => -1, 'tip' => '建议使用PDO扩展，否则NotORM无法使用PDO进行数据库操作'),
        'memcache'  => array('name' => 'Memcache扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用Memcache缓存'),
        'mcrypt'    => array('name' => 'Mcrypt扩展', 'status' => 0, 'tip' => '无此扩展时，不能使用mcrypt进行加密处理'),
        'runtime'   => array('name' => '目录权限', 'status' => -1, 'tip' => '根目录、日志及配置文件目录若缺少写入权限，则不能写入日记和进行文件缓存以及接下配置无法生效'),
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
    if (extension_loaded('mcrypt')) {
        $checkList['mcrypt']['status'] = 1;
    }
    $runtimePath = dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Runtime'));
    $runtimePath = file_exists($runtimePath) ? realpath($runtimePath) : $runtimePath;
    $checkList['runtime']['tip'] = $runtimePath . '<br>' . $checkList['runtime']['tip'];

    $configPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Config'));
    $configPath = file_exists($configPath) ? realpath($configPath) : $configPath;
    $checkList['runtime']['tip'] = $configPath . '<br>' . $checkList['runtime']['tip'];

    $publicPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Public'));
    $publicPath = file_exists($publicPath) ? realpath($publicPath) : $publicPath;
    $checkList['runtime']['tip'] = $publicPath . '<br>' . $checkList['runtime']['tip'];

    if (is_writeable($runtimePath) && is_writeable($configPath) && is_writable($publicPath)) {
        $checkList['runtime']['status'] =  1;
    }

    include dirname(__FILE__) . D_S . '_step1.php';
    break;
    //第2步：系统配置
case 2:
    include dirname(__FILE__) . D_S . '_step2.php';
    break;
    //第3步：接口请求
case 3:
    if (empty($_POST['doSubmit']) || empty($_POST)) {
        header('Location: ./?step=1');
        exit(0);
    }

    //数据库配置文件
    $search = array(
        '{project}',
        '{host}',
        '{name}',
        '{user}',
        '{password}',
        '{port}',
        '{charset}',
        '{prefix}',
    );
    $replace = array(
        strtolower($_POST['project']),
        $_POST['host'],
        $_POST['name'],
        $_POST['user'],
        $_POST['password'],
        $_POST['port'],
        $_POST['charset'],
        $_POST['prefix'],
    );
    $configDbsContent = str_replace($search, $replace, getConfigDbsTpl());
    file_put_contents(
        dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Config', 'dbs.php')),
        $configDbsContent
    );

    //Project
    $project = ucwords($_POST['project']);
    $appPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', $project,));
    $demoPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Demo',));
    if (!file_exists($appPath)) {
        //项目目录
        mkdir($appPath . D_S);
        mkdir($appPath . D_S . 'Api');
        mkdir($appPath . D_S . 'Domain');
        mkdir($appPath . D_S . 'Model');
        mkdir($appPath . D_S . 'Common');

        copy(
            $demoPath . D_S . 'Api' . D_S . 'Default.php',
            $appPath . D_S . 'Api' . D_S . 'Default.php'
        );

        mkdir($appPath . D_S . 'Tests');
        mkdir($appPath . D_S . 'Tests' . D_S . 'Api');
        mkdir($appPath . D_S . 'Tests' . D_S . 'Domain');
        mkdir($appPath . D_S . 'Tests' . D_S . 'Model');
        mkdir($appPath . D_S . 'Tests' . D_S . 'Common');

        //单元测试
        copy(
            $demoPath . D_S . 'Tests' . D_S . 'test_env.php',
            $appPath . D_S . 'Tests'  . D_S . 'test_env.php'
        );
        file_put_contents(
            $appPath . D_S . 'Tests'  . D_S . 'test_env.php',
            str_replace('Demo', $project, file_get_contents($appPath . D_S . 'Tests'  . D_S . 'test_env.php'))
        );

        copy(
            $demoPath . D_S . 'Tests' . D_S . 'Api' . D_S . 'Api_Default_Test.php',
            $appPath . D_S . 'Tests' . D_S . 'Api' . D_S . 'Api_Default_Test.php'
        );
        copy(
            $demoPath . D_S . 'Tests' . D_S . 'phpunit.xml',
            $appPath . D_S . 'Tests' . D_S . 'phpunit.xml'
        );

        //访问入口
        $appPublicPath = dirname(__FILE__) . implode(D_S, array('', '..', strtolower($project), ));
        $demoPublicPath = dirname(__FILE__) . implode(D_S, array('', '..', 'demo',));

        mkdir($appPublicPath);

        copy(
            $demoPublicPath . D_S . 'checkApiParams.php',
            $appPublicPath . D_S . 'checkApiParams.php'
        );
        copy(
            $demoPublicPath . D_S . 'listAllApis.php',
            $appPublicPath . D_S . 'listAllApis.php'
        );
        copy(
            $demoPublicPath . D_S . 'index.php',
            $appPublicPath . D_S . 'index.php'
        );

        // 挂载项目
        foreach (array('checkApiParams.php', 'listAllApis.php', 'index.php') as $publicFile) {
            file_put_contents(
                $appPublicPath . D_S . $publicFile,
                str_replace('Demo', $project, file_get_contents($demoPublicPath . D_S . $publicFile))
            );
        }
    }

    @touch('_install.lock');

    //请求链接
    $relatePath = substr($_SERVER['REQUEST_URI'], 0, stripos($_SERVER['REQUEST_URI'], '/install/'));
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $relatePath . '/' . strtolower($project);
    include dirname(__FILE__) . D_S . '_step3.php';
    break;
default:
    include dirname(__FILE__) . D_S . '_start.php';
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
        'db_{project}' => array(                   //服务器标记
            'host'      => '{host}',               //数据库域名
            'name'      => '{name}',               //数据库名字
            'user'      => '{user}',               //数据库用户名
            'password'  => '{password}',           //数据库密码
            'port'      => '{port}',               //数据库端口
            'charset'   => '{charset}',            //数据库字符集
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
            'prefix' => '{prefix}',                                     //表名前缀
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