<?php
/**
 * 分布式的DB存储
 *
 * - 可定义每个表的存储路由和规则，匹配顺序：
 *   自定义区间匹配 -> 自定义缺省匹配 -> 默认区间匹配 -> 默认缺省匹配
 * - 底层依赖NotORM实现数据库的操作
 * 
 *      //需要提供以下格式的DB配置
 *      $config = array(
 *        //可用的DB服务器集群
 *       'servers' => array(
 *           'db_demo' => array(
 *               'host'      => 'localhost',                //数据库域名
 *               'name'      => 'test',                     //数据库名字
 *               'user'      => 'root',                     //数据库用户名
 *               'password'  => '123456',	                //数据库密码
 *               'port'      => '3306',		                //数据库端口
 *           ),
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
 *      $notorm = new PhalApi_DB_NotORM($config);
 *
 *      //根据ID对3取模的映射获取数据
 *      $rs = $notorm->demo_0->select('*')->where('id = 10')->fetch();
 *      $rs = $notorm->demo_1->select('*')->where('id = 11')->fetch();
 *
 * @link: http://www.notorm.com/
 * @author dogstar 2014-11-22
 */

require_once PHALAPI_ROOT . DIRECTORY_SEPARATOR . 'NotORM' . DIRECTORY_SEPARATOR . 'NotORM.php';

class PhalApi_DB_NotORM /** implements PhalApi_DB */
{
    protected $_notorms = array();

    protected $_pdos = array();

    protected $_configs = array();

    protected $debug = false;

    public function __construct($configs, $debug = false)
    {
        $this->_configs = $configs;

        $this->debug = $debug;
    }

    public function __get($name)
    {
        $notormKey = $this->createNotormKey($name);

        if (!isset($this->_notorms[$notormKey])) {
            list($tableName, $suffix) = $this->parseName($name);
            $router = $this->getDBRouter($tableName, $suffix);

            $structure = new NotORM_Structure_Convention(
                $router['key'], '%s_id', '%s', $router['prefix']);
            $this->_notorms[$notormKey] = new NotORM($router['pdo'], $structure);

            $this->_notorms[$notormKey]->debug = $this->debug;

            if ($router['isNoSuffix']) {
                $name = $tableName;
            }
        }

        return $this->_notorms[$notormKey]->$name;
    }

    public function __set($name, $value)
    {
        foreach ($this->_notorms as $key => $notorm) {
            $notorm->$name = $value;
        }
    }

    protected function createNotormKey($tableName)
    {
        return '__' . $tableName . '__';
    }

    protected function parseName($name)
    {
        $tableName = $name;
        $suffix = null;

        $pos = strrpos($name, '_');
        if ($pos !== false) {
            $tableId = substr($name, $pos + 1);
            if (is_numeric($tableId)) {
                $tableName = substr($name, 0, $pos);
                $suffix = intval($tableId);
            }
        }

        return array($tableName, $suffix);
    }

    protected function getDBRouter($tableName, $suffix)
    {
        $rs = array('prefix' => '', 'key' => '', 'pdo' => null, 'isNoSuffix' => false);

        $defaultMap = !empty($this->_configs['tables']['__default__']) 
            ? $this->_configs['tables']['__default__'] : array();
        $tableMap = !empty($this->_configs['tables'][$tableName]) 
            ? $this->_configs['tables'][$tableName] : $defaultMap;

        if (empty($tableMap)) {
            throw new PhalApi_Exception_InternalServerError(
                T('No table map config for {tableName}', array('tableName' => $tableName))
            );
        }

        $dbKey = null;
        $dbDefaultKey = null;
        if (!isset($tableMap['map'])) {
            $tableMap['map'] = array();
        }
        foreach ($tableMap['map'] as $map) {
            $isMatch = false;

            if ((isset($map['start']) && isset($map['end']))) {
                if ($suffix !== null && $suffix >= $map['start'] && $suffix <= $map['end']) {
                    $isMatch = true;
                }
            } else {
                $dbDefaultKey = $map['db'];
                if ($suffix === null) {
                    $isMatch = true;
                }
            }

            if ($isMatch) {
                $dbKey = isset($map['db']) ? trim($map['db']) : null;
                break;
            }
        }
        //try to use default map if no perfect match
        if ($dbKey === null) {
            $dbKey = $dbDefaultKey;
            $rs['isNoSuffix'] = true;
        }

        if ($dbKey === null) {
            throw new PhalApi_Exception_InternalServerError(
                T('No db router match for {tableName}', array('tableName' => $tableName))
            );
        }

        $rs['pdo'] = $this->getPdo($dbKey);
        $rs['prefix'] = isset($tableMap['prefix']) ? trim($tableMap['prefix']) : '';
        $rs['key'] = isset($tableMap['key']) ? trim($tableMap['key']) : 'id';

        return $rs;
    }

    protected function getPdo($dbKey)
    {
        if (!isset($this->_pdos[$dbKey])) {
            $dbCfg = isset($this->_configs['servers'][$dbKey]) 
                ? $this->_configs['servers'][$dbKey] : array();

            $this->_pdos[$dbKey] = new PDO(
                'mysql:dbname=' . $dbCfg['name'] . ';host=' . $dbCfg['host'],
                $dbCfg['user'],
                $dbCfg['password'],
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
            );
        }

        return $this->_pdos[$dbKey];
    }
}

