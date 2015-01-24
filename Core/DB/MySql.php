<?php
/**
 * Core_DB_MySql 数据库类
 * 外观模式，基于RedBean实现
 *
 * 1、统一添加表前缀  //TODO
 * 2、简化对DB的操作
 * 3、对RedBean添加防腐层
 *
 * @author: dogstar 2014-10-02
 */

Core_DI::one()->loader->loadFile('ThirdParty/RedBeanPHP/rb.php');

class Core_DB_MySql implements Core_DB
{
	private $config = array('host' => 'localhost', 'name' => '', 'user' => '', 'password' => '');
	
	/** ------------------ 接口层对外操作 ------------------ **/
	
    public function __construct($config)
    {
    	$this->config = $config;
    }
    
    public function connect()
    {
    	R::setup('mysql:host=' . $this->config['host'] . ';dbname='.$this->config['name'],
        	$this->config['user'], $this->config['password']);
    }
    
    public function disconnect()
    {
    	R::close();
    }
    
    public function getTableName($tableName)
    {
    	return $this->config['prefix'] . $tableName;
    }
    
    /** ------------------ 框架层的外观模式 ：通用的SQL查询与执行 ------------------ **/

	public function exec($sql, $bindings = array())
	{
		return R::exec($sql, $bindings);
	}

	/** ------------------ 框架层的外观模式 ：查询相关操作(R) ------------------ **/
	
	public function count($tableName, $addSQL = '', $bindings = array())
	{
		return R::count($this->getTableName($tableName), $addSQL, $bindings);
	}
	
	public function getRow($tableName, $fields = '*', $addSql = '1', $bindings = array())
    {
        $sql = sprintf('SELECT %s FROM %s WHERE %s',
            $fields, $this->getTableName($tableName), $addSql);

        $rs = R::getRow($sql, $bindings);
        return $rs !== NULL ? $rs : array();
	}
	
	public function getAll($tableName, $fields = '*', $addSql = '', $bindings = array(), $order = '', $start = 0, $len = 0)
    {
        $sql = sprintf('SELECT %s FROM %s WHERE %s',
            $fields, $this->getTableName($tableName), $addSql);

        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }

        if ($start >= 0 && $len >= 0) {
            $sql .= " LIMIT {$start}, {$len}";
        }

        $rs = R::getAll($sql, $bindings);
        return $rs !== NULL ? $rs : array();
	}
	
	/** ------------------ 框架层的外观模式 ：更新相关操作(U) ------------------ **/

	public function add($tableName, array $data)
	{
        if (empty($data)) {
            return 0;
        }

		$fields = implode(',', array_keys($data));
		$cols = implode(',', array_fill(0, count($data), '?'));
        $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)',
            $this->getTableName($tableName), $fields, $cols);
		$this->exec($sql, array_values($data));

		return R::getDatabaseAdapter()->getInsertID();
	}
	
    public function update($tableName, array $data, $where)
    {
        if (empty($data) || empty($where)) {
            return 0;
        }

		$updateCols = array();
		foreach ($data as $col => $val) {
			$updateCols[] = $col . ' = ?';
        }

        $sql = sprintf('UPDATE %s SET %s WHERE %s', 
            $this->getTableName($tableName), implode(', ', $updateCols), $where);

		return $this->exec($sql, array_values($data));
	}
	
	/** ------------------ 框架层的外观模式 ：删除相关操作(D) ------------------ **/

    public function delete($tableName, $query)
    {
        $sql = 'DELETE FROM ' . $this->getTableName($tableName) . ' WHERE ';
        $bindings = array();

        if (empty($query)) {
            return;
        }

        if (is_array($query)) {
            $cols = array();
            foreach ($query as $key => $val) {
                $cols[] = $key . ' = ?';
            }
            $sql .= implode(', ', $cols);
            $bindings = array_values($query);
        } else {
            $sql .= $query;
        }

        return $this->exec($sql, $bindings);
    }

	/** ------------------ 框架层的外观模式 ：开发调试操作 ------------------ **/
	
	public function testConnection()
	{
		return R::testConnection();
	}

	public function dump($data)
	{
		return R::dump($data);
	}

    public function debug()
    {
        return R::debug();
    }
}
