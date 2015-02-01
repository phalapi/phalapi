<?php

/** Information about tables and columns structure
*/
interface NotORM_Structure {
	
	/** Get primary key of a table in $db->$table()
	* @param string
	* @return string
	*/
	function getPrimary($table);
	
	/** Get column holding foreign key in $table[$id]->$name()
	* @param string
	* @param string
	* @return string
	*/
	function getReferencingColumn($name, $table);
	
	/** Get target table in $table[$id]->$name()
	* @param string
	* @param string
	* @return string
	*/
	function getReferencingTable($name, $table);
	
	/** Get column holding foreign key in $table[$id]->$name
	* @param string
	* @param string
	* @return string
	*/
	function getReferencedColumn($name, $table);
	
	/** Get table holding foreign key in $table[$id]->$name
	* @param string
	* @param string
	* @return string
	*/
	function getReferencedTable($name, $table);
	
	/** Get sequence name, used by insert
	* @param string
	* @return string
	*/
	function getSequence($table);
	
}



/** Structure described by some rules
*/
class NotORM_Structure_Convention implements NotORM_Structure {
	protected $primary, $foreign, $table, $prefix;
	
	/** Create conventional structure
	* @param string %s stands for table name
	* @param string %1$s stands for key used after ->, %2$s for table name
	* @param string %1$s stands for key used after ->, %2$s for table name
	* @param string prefix for all tables
	*/
	function __construct($primary = 'id', $foreign = '%s_id', $table = '%s', $prefix = '') {
		$this->primary = $primary;
		$this->foreign = $foreign;
		$this->table = $table;
		$this->prefix = $prefix;
	}
	
	function getPrimary($table) {
		return sprintf($this->primary, $this->getColumnFromTable($table));
	}
	
	function getReferencingColumn($name, $table) {
		return $this->getReferencedColumn(substr($table, strlen($this->prefix)), $this->prefix . $name);
	}
	
	function getReferencingTable($name, $table) {
		return $this->prefix . $name;
	}
	
	function getReferencedColumn($name, $table) {
		return sprintf($this->foreign, $this->getColumnFromTable($name), substr($table, strlen($this->prefix)));
	}
	
	function getReferencedTable($name, $table) {
		return $this->prefix . sprintf($this->table, $name, $table);
	}
	
	function getSequence($table) {
		return null;
	}
	
	protected function getColumnFromTable($name) {
		if ($this->table != '%s' && preg_match('(^' . str_replace('%s', '(.*)', preg_quote($this->table)) . '$)', $name, $match)) {
			return $match[1];
		}
		return $name;
	}
	
}



/** Structure reading meta-informations from the database
*/
class NotORM_Structure_Discovery implements NotORM_Structure {
	protected $connection, $cache, $structure = array();
	protected $foreign;
	
	/** Create autodisovery structure
	* @param PDO
	* @param NotORM_Cache
	* @param string use "%s_id" to access $name . "_id" column in $row->$name
	*/
	function __construct(PDO $connection, NotORM_Cache $cache = null, $foreign = '%s') {
		$this->connection = $connection;
		$this->cache = $cache;
		$this->foreign = $foreign;
		if ($cache) {
			$this->structure = $cache->load("structure");
		}
	}
	
	/** Save data to cache
	*/
	function __destruct() {
		if ($this->cache) {
			$this->cache->save("structure", $this->structure);
		}
	}
	
	function getPrimary($table) {
		$return = &$this->structure["primary"][$table];
		if (!isset($return)) {
			$return = "";
			foreach ($this->connection->query("EXPLAIN $table") as $column) {
				if ($column[3] == "PRI") { // 3 - "Key" is not compatible with PDO::CASE_LOWER
					if ($return != "") {
						$return = ""; // multi-column primary key is not supported
						break;
					}
					$return = $column[0];
				}
			}
		}
		return $return;
	}
	
	function getReferencingColumn($name, $table) {
		$name = strtolower($name);
		$return = &$this->structure["referencing"][$table];
		if (!isset($return[$name])) {
			foreach ($this->connection->query("
				SELECT TABLE_NAME, COLUMN_NAME
				FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_NAME = " . $this->connection->quote($table) . "
				AND REFERENCED_COLUMN_NAME = " . $this->connection->quote($this->getPrimary($table)) //! may not reference primary key
			) as $row) {
				$return[strtolower($row[0])] = $row[1];
			}
		}
		return $return[$name];
	}
	
	function getReferencingTable($name, $table) {
		return $name;
	}
	
	function getReferencedColumn($name, $table) {
		return sprintf($this->foreign, $name);
	}
	
	function getReferencedTable($name, $table) {
		$column = strtolower($this->getReferencedColumn($name, $table));
		$return = &$this->structure["referenced"][$table];
		if (!isset($return[$column])) {
			foreach ($this->connection->query("
				SELECT COLUMN_NAME, REFERENCED_TABLE_NAME
				FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_SCHEMA = DATABASE()
				AND TABLE_NAME = " . $this->connection->quote($table) . "
			") as $row) {
				$return[strtolower($row[0])] = $row[1];
			}
		}
		return $return[$column];
	}
	
	function getSequence($table) {
		return null;
	}
	
}
