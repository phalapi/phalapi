<?php
define('PHALAPI_INSTALL', TRUE);
define('D_S', DIRECTORY_SEPARATOR);

$step = isset($_GET['step']) ? intval($_GET['step']) : 0;

switch ($step) {
    /**
     * Step 1: environment detection
     */
case 1:
    if (file_exists('_install.lock')) {
        $error = 'Your project has been installed, please do not install again. Furthermore, we recommend delete the folder ./install manually.';
        include dirname(__FILE__) . D_S . '_error.php';
        exit(0);
    }
    
    // -1: must support but not, 0: optinal support but not, 1: perfect support
    $checkList = array(
        'php'       => array('name' => 'PHP version', 'status' => -1, 'tip' => 'suggest PHP >= 5.3.3, or DI fail to use anonymous function'),
        'pdo'       => array('name' => 'database module', 'status' => -1, 'tip' => 'suggest use PDO library, or NotORM can not work'),
        'memcache'  => array('name' => 'Memcache Library', 'status' => 0, 'tip' => 'no Memcache supported without this library'),
        'mcrypt'    => array('name' => 'Mcrypt Library', 'status' => 0, 'tip' => 'no mcrypt supported without this library'),
        'runtime'   => array('name' => 'Runtime Permision', 'status' => -1, 'tip' => 'both logs and cache data can not written into runtime without permision'),
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
    /**
     * Step 2: system configuration
     */
case 2:
    include dirname(__FILE__) . D_S . '_step2.php';
    break;
    /**
     * Step 3: ready to create API project
     */
case 3:
    if (empty($_POST['doSubmit']) || empty($_POST)) {
        header('Location: ./?step=1');
        exit(0);
    }

    // database config file
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

    // Project
    $project = ucwords($_POST['project']);
    $appPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', $project,));
    $demoPath = dirname(__FILE__) . implode(D_S, array('', '..', '..', 'Demo',));
    if (!file_exists($appPath)) {
        // API project folders
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

        // unit tests
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

        // API public entrance
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

        // replace Demo with the new project name
        foreach (array('checkApiParams.php', 'listAllApis.php', 'index.php') as $publicFile) {
            file_put_contents(
                $appPublicPath . D_S . $publicFile,
                str_replace('Demo', $project, file_get_contents($demoPublicPath . D_S . $publicFile))
            );
        }
    }

    @touch('_install.lock');

    // API request url
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
 * Database Configuration
 *
 * - support multi databases and tables
 * - support customing table routes
 */

return array(
    /**
     * Customing Table Routes
     */
    'servers' => array(
        'db_{project}' => array(                         // server ID
            'host'      => '{host}',                     // database host
            'name'      => '{name}',                     // database name
            'user'      => '{user}',                     // database username
            'password'  => '{password}',	             // database password
            'port'      => '{port}',                     // database port
            'charset'   => '{charset}',                  // database charset
        ),
    ),

    /**
     * Customing Table Routes
     */
    'tables' => array(
        // Common Defatult Routes
        '__default__' => array(
            'prefix' => '{prefix}',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_{project}'),
            ),
        ),

        /**
        'demo' => array(                                                // table name
            'prefix' => '{prefix}',                                     // table prefix
            'key' => 'id',                                              // table primary key
            'map' => array(                                             // table route map
                array('db' => 'db_{project}'),                               // single table: array('db' => server ID)
                array('start' => 0, 'end' => 2, 'db' => 'db_{project}'),     // multi tables: array('start' => start pos, 'end' => end pos, 'db' => server ID)
            ),
        ),
         */
    ),
);

EOT;

    return $configDbs;
}
