<?php
namespace PhalApi\Database;

use PDO;
use PDOException;
use PhalApi\Database;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\NotORM\Lite as NotORMLite;

/**
 * PhalApi\Database\NotORM 分布式的DB存储
 *
 * 基于NotORM的数据库操作，支持分布式
 * 
 * - 可定义每个表的存储路由和规则，匹配顺序：
 *   自定义区间匹配 -> 自定义缺省匹配 -> 默认区间匹配 -> 默认缺省匹配
 * - 底层依赖NotORM实现数据库的操作
 * 
 * <br>使用示例：<br>
```
 *      //需要提供以下格式的DB配置
 *      $config = array(
 *        //可用的DB服务器集群
 *       'servers' => array(
 *          'db_demo' => array(
 *              'host'      => 'localhost',             //数据库域名
 *              'name'      => 'phalapi',               //数据库名字
 *              'user'      => 'root',                  //数据库用户名
 *              'password'  => '',	                    //数据库密码
 *              'port'      => '3306',		            //数据库端口
 *              'charset'   => 'UTF8',                  //数据库字符集
 *          ),
 *       ),
 *
 *        //自定义表的存储路由
 *       'tables' => array(
 *           '__default__' => array(                                            //默认
 *               'prefix' => 'tbl_',
 *               'key' => 'id',
 *               'map' => array(
 *                   array('db' => 'db_demo'),                                  //默认缺省
 *                   array('start' => 0, 'end' => 2, 'db' => 'db_demo'),        //默认区间
 *               ),
 *           ),
 *           'demo' => array(                                                   //自定义
 *               'prefix' => 'tbl_',
 *               'key' => 'id',
 *               'map' => array(
 *                   array('db' => 'db_demo'),                                  //自定义缺省
 *                   array('start' => 0, 'end' => 2, 'db' => 'db_demo'),        //定义区间
 *               ),
 *           ),
 *       ),
 *      );
 *
 *      $notorm = new PhalApi\DB\NotORM($config);
 *
 *      //根据ID对3取模的映射获取数据
 *      $rs = $notorm->demo_0->select('*')->where('id', 10)->fetch();
 *      $rs = $notorm->demo_1->select('*')->where('id', 11)->fetch();
```
 *
 * @property string table_name 数据库表名
 *
 * @package     PhalApi\DB
 * @link        http://www.notorm.com/
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-05
 */

class NotORMDatabase /** implements Database */ {

	/**
	 * @var NotORM $_notorms NotORM的实例池
	 */
    protected $_notorms = array();

    /**
     * @var PDO $_pdos PDO连接池，统一管理，避免重复连接
     */
    protected $_pdos = array();

    /**
     * @var array $_configs 数据库配置 
     */
    protected $_configs = array();

    /**
     * @var boolean 是否开启调试模式，调试模式下会输出全部执行的SQL语句和对应消耗的时间
     */
    protected $debug = FALSE;

    /**
     * @var boolean 是否保持原来数据库结果集中以主键为KEY的返回方式（默认不使用）
     */
    protected $isKeepPrimaryKeyIndex = FALSE;

    /**
    * @var array $tablenameAliasMap 表名的别名映射关系，主要用于分表回退时的缺省表名修正，避免因类缓存而导致bug，[原来的完全表名 => 修正的新表名]
    */
    protected $tablenameAliasMap = array();

    /**
     * @param array $configs 数据库配置 
     * @param boolean $debug 是否开启调试模式
     */
    public function __construct($configs, $debug = FALSE) {
        $this->_configs = $configs;

        $this->debug = $debug;
    }

    public function __get($name) {
        $notormKey = $this->createNotormKey($name);

        if (!isset($this->_notorms[$notormKey])) {
            list($tableName, $suffix) = $this->parseName($name);
            $router = $this->getDBRouter($tableName, $suffix);

            $this->_notorms[$notormKey] = new NotORMLite($router['pdo']);
            $structure = new \NotORM_Structure_Convention(
                $router['key'], '%s_id', '%s', $router['prefix']);
            $this->_notorms[$notormKey]->setStructure($structure);

            // 调试模式与回调函数
            $this->_notorms[$notormKey]->debug = $this->debug;
            $this->_notorms[$notormKey]->debugTimer = array(\PhalApi\DI()->tracer, 'sql');

            // 是否在结果集中保持主键作为索引
            $this->_notorms[$notormKey]->isKeepPrimaryKeyIndex = $this->isKeepPrimaryKeyIndex;

            if ($router['isNoSuffix']) {
                $this->tablenameAliasMap[$name] = $tableName; // 纪录修正的映射关系，来源于小白接口发现的bug
                $name = $tableName;
            }
        } else {
            $name = isset($this->tablenameAliasMap[$name]) ? $this->tablenameAliasMap[$name] : $name; // 缓存下的修正
        }

        return $this->_notorms[$notormKey]->$name;
    }

    public function __set($name, $value) {
        foreach ($this->_notorms as $notorm) {
            $notorm->$name = $value;
        }
    }

    protected function createNotormKey($tableName) {
        return '__' . $tableName . '__';
    }

    /**
     * 解析分布式表名
     * 表名  + ['_' + 数字后缀]，如：user_0, user_1, ... user_100
     * @param string $name
     */
    protected function parseName($name) {
        $tableName = $name;
        $suffix = NULL;

        $pos = strrpos($name, '_');
        if ($pos !== FALSE) {
            $tableId = substr($name, $pos + 1);
            if (is_numeric($tableId)) {
                $tableName = substr($name, 0, $pos);
                $suffix = intval($tableId);
            }
        }

        return array($tableName, $suffix);
    }

    /**
     * 获取分布式数据库路由
     * @param string $tableName 数据库表名
     * @param string $suffix 分布式下的表后缀
     * @return array 数据库配置
     * @throws Exception_InternalServerError
     */
    protected function getDBRouter($tableName, $suffix) {
        $rs = array('prefix' => '', 'key' => '', 'pdo' => NULL, 'isNoSuffix' => FALSE);

        if (preg_match('/^(\S+)\.(\S+)$/', $tableName, $match)) {
            $server = $match[1];
            if (!isset($this->_configs['tables'][$tableName])) {
                $this->_configs['tables'][$tableName] = array('map' => array(array('db' => $server)));
            }
        }

        $defaultMap = !empty($this->_configs['tables']['__default__']) 
            ? $this->_configs['tables']['__default__'] : array();
        $tableMap = !empty($this->_configs['tables'][$tableName]) 
            ? $this->_configs['tables'][$tableName] : $defaultMap;

        if (empty($tableMap)) {
            throw new InternalServerErrorException(
                \PhalApi\T('No table map config for {tableName}', array('tableName' => $tableName))
            );
        }

        $dbKey = NULL;
        $dbDefaultKey = NULL;
        if (!isset($tableMap['map'])) {
            $tableMap['map'] = array();
        }
        foreach ($tableMap['map'] as $map) {
            $isMatch = FALSE;

            if ((isset($map['start']) && isset($map['end']))) {
                if ($suffix !== NULL && $suffix >= $map['start'] && $suffix <= $map['end']) {
                    $isMatch = TRUE;
                }
            } else {
                $dbDefaultKey = $map['db'];
                if ($suffix === NULL) {
                    $isMatch = TRUE;
                }
            }

            if ($isMatch) {
                $dbKey = isset($map['db']) ? trim($map['db']) : NULL;
                break;
            }
        }
        //try to usdbKeye default map if no perfect match
        if ($dbKey === NULL) {
            $dbKey = $dbDefaultKey;
            $rs['isNoSuffix'] = TRUE;
        }

        if ($dbKey === NULL) {
            throw new InternalServerErrorException(
                \PhalApi\T('No db router match for {tableName}', array('tableName' => $tableName))
            );
        }

        $rs['pdo'] = $this->getPdo($dbKey);
        $rs['prefix'] = isset($tableMap['prefix']) ? trim($tableMap['prefix']) : '';
        $rs['key'] = isset($tableMap['key']) ? trim($tableMap['key']) : 'id';

        return $rs;
    }

    /**
     * 获取 PDO连接
     * @param string $dbKey 数据库表名唯一KEY
     * @return PDO
     */
    protected function getPdo($dbKey) {
        if (!isset($this->_pdos[$dbKey])) {
            $dbCfg = isset($this->_configs['servers'][$dbKey]) 
                ? $this->_configs['servers'][$dbKey] : array();

            if (empty($dbCfg)) {
                throw new InternalServerErrorException(
                    \PhalApi\T('no such db:{db} in servers', array('db' => $dbKey)));
            }

            try {
                $this->_pdos[$dbKey] = $this->createPDOBy($dbCfg);
            } catch (PDOException $ex) {
                //异常时，接口异常返回，并隐藏数据库帐号信息
                $errorMsg = \PhalApi\T('can not connect to database: {db}', array('db' => $dbKey));
                if (\PhalApi\DI()->debug) {
                    $errorMsg = \PhalApi\T('can not connect to database: {db}, code: {code}, cause: {msg}', 
                        array('db' => $dbKey, 'code' => $ex->getCode(), 'msg' => $ex->getMessage()));
                }
                throw new InternalServerErrorException($errorMsg);
            }
        }

        return $this->_pdos[$dbKey];
    }

    /**
     * 针对MySQL的PDO链接，如果需要采用其他数据库，可重载此函数
     * @link https://www.php.net/manual/en/book.pdo.php
     * @param array $dbCfg 数据库配置
     * @return PDO
     */
    protected function createPDOBy($dbCfg) {
        // 默认mysql
        $type = !empty($dbCfg['type']) ? strtolower($dbCfg['type']) : 'mysql';
        $dsn = sprintf('mysql:dbname=%s;host=%s;port=%d',
            $dbCfg['name'], 
            isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost', 
            isset($dbCfg['port']) ? $dbCfg['port'] : 3306
        );
        
        if ($type == 'sqlserver' || $type == 'sqlsrv') {  // 支持sql server
            $dsn = sprintf('sqlsrv:Server=%s,%d;Database=%s',
                isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost', 
                isset($dbCfg['port']) ? $dbCfg['port'] : 1433, 
                $dbCfg['name']
            );
        } else if ($type == 'dblib_sqlserver') {  // 支持sql server(通过dblib驱动)
            $dsn = sprintf('dblib:host=%s:%d;dbname=%s',
                isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost', 
                isset($dbCfg['port']) ? $dbCfg['port'] : 1433, 
                $dbCfg['name']
            );
        } else if ($type == 'pgsql') {  // 支持postgreSQL
            $dsn = sprintf('pgsql:dbname=%s;host=%s;port=%d',
                $dbCfg['name'],
                isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost',
                isset($dbCfg['port']) ? $dbCfg['port'] : 3306
            );
        }

        // 创建PDO连接
        $pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['password']);

        // 设置编码
        $charset = isset($dbCfg['charset']) ? $dbCfg['charset'] : 'UTF8';
        $pdo->exec("SET NAMES '{$charset}'");

        return $pdo;
    }

    /**
     * 断开数据库链接
     */
    public function disconnect() {
        foreach ($this->_pdos as $dbKey => $pdo) {
            $this->_pdos[$dbKey] = NULL;
            unset($this->_pdos[$dbKey]);
        }

        foreach ($this->_notorms as $notormKey => $notorm) {
            $this->_notorms[$notormKey] = NULL;
            unset($this->_notorms[$notormKey]);
        }
    }

    /**
     * 为历史修改埋单：保持原来数据库结果集中以主键为KEY的返回方式
     *
     * - PhalSpi 1.3.1 及以下版本才需要用到此切换动作
     * - 涉及影响的数据库操作有：fetchAll()/fetchRows()等
     *
     * ＠return DB_NotORM
     */
    public function keepPrimaryKeyIndex() {
        $this->isKeepPrimaryKeyIndex = TRUE;
        return $this;
    }

    /** ------------------ 事务操作 ------------------ **/

    /**
     * 开启数据库事务
     * @param string $whichDB 指定数据库标识
     * @return NULL
     */
    public function beginTransaction($whichDB) {
        $this->getPdo($whichDB)->beginTransaction();
    }

    /**
     * 提交数据库事务
     * @param string $whichDB 指定数据库标识
     * @return NULL
     */
    public function commit($whichDB) {
        $this->getPdo($whichDB)->commit();
    }

    /**
     * 回滚数据库事务
     * @param string $whichDB 指定数据库标识
     * @return NULL
     */
    public function rollback($whichDB) {
        $this->getPdo($whichDB)->rollback();
    }
}
