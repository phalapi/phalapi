<?php

/** Single row representation
*/
class NotORM_Row extends NotORM_Abstract implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable {
	private $modified = array();
	protected $row, $result, $primary;
	
	/** @access protected must be public because it is called from Result */
	function __construct(array $row, NotORM_Result $result) {
		$this->row = $row;
		$this->result = $result;
		if (array_key_exists($result->primary, $row)) {
			$this->primary = $row[$result->primary];
		}
	}
	
	/** Get primary key value
	* @return string
	*/
	function __toString() {
		return (string) $this[$this->result->primary]; // (string) - PostgreSQL returns int
	}
	
	/** Get referenced row
	* @param string
	* @return NotORM_Row or null if the row does not exist
	*/
	function __get($name) {
		$column = $this->result->notORM->structure->getReferencedColumn($name, $this->result->table);
		$referenced = &$this->result->referenced[$name];
		if (!isset($referenced)) {
			$keys = array();
			foreach ($this->result->rows as $row) {
				if ($row[$column] !== null) {
					$keys[$row[$column]] = null;
				}
			}
			if ($keys) {
				$table = $this->result->notORM->structure->getReferencedTable($name, $this->result->table);
				$referenced = new NotORM_Result($table, $this->result->notORM);
				$referenced->where("$table." . $this->result->notORM->structure->getPrimary($table), array_keys($keys));
			} else {
				$referenced = array();
			}
		}
		if (!isset($referenced[$this[$column]])) { // referenced row may not exist
			return null;
		}
		return $referenced[$this[$column]];
	}
	
	/** Test if referenced row exists
	* @param string
	* @return bool
	*/
	function __isset($name) {
		return ($this->__get($name) !== null);
	}
	
	/** Store referenced value
	* @param string
	* @param NotORM_Row or null
	* @return null
	*/
	function __set($name, NotORM_Row $value = null) {
		$column = $this->result->notORM->structure->getReferencedColumn($name, $this->result->table);
		$this[$column] = $value;
	}
	
	/** Remove referenced column from data
	* @param string
	* @return null
	*/
	function __unset($name) {
		$column = $this->result->notORM->structure->getReferencedColumn($name, $this->result->table);
		unset($this[$column]);
	}
	
	/** Get referencing rows
	* @param string table name
	* @param array (["condition"[, array("value")]])
	* @return NotORM_MultiResult
	*/
	function __call($name, array $args) {
		$table = $this->result->notORM->structure->getReferencingTable($name, $this->result->table);
		$column = $this->result->notORM->structure->getReferencingColumn($table, $this->result->table);
		$return = new NotORM_MultiResult($table, $this->result, $column, $this[$this->result->primary]);
		$return->where("$table.$column", array_keys((array) $this->result->rows)); // (array) - is null after insert
		if ($args) {
			call_user_func_array(array($return, 'where'), $args);
		}
		return $return;
	}
	
	/** Update row
	* @param array or null for all modified values
	* @return int number of affected rows or false in case of an error
	*/
	function update($data = null) {
		// update is an SQL keyword
		if (!isset($data)) {
			$data = $this->modified;
		}
		$result = new NotORM_Result($this->result->table, $this->result->notORM);
		$return = $result->where($this->result->primary, $this->primary)->update($data);
		$this->primary = $this[$this->result->primary];
		return $return;
	}
	
	/** Delete row
	* @return int number of affected rows or false in case of an error
	*/
	function delete() {
		// delete is an SQL keyword
		$result = new NotORM_Result($this->result->table, $this->result->notORM);
		$return = $result->where($this->result->primary, $this->primary)->delete();
		$this->primary = $this[$this->result->primary];
		return $return;
	}
	
	protected function access($key, $delete = false) {
		if ($this->result->notORM->cache && !isset($this->modified[$key]) && $this->result->access($key, $delete)) {
			$id = (isset($this->primary) ? $this->primary : $this->row);
			$this->row = $this->result[$id]->row;
		}
	}
	
	// IteratorAggregate implementation
	
	function getIterator() {
		$this->access(null);
		return new ArrayIterator($this->row);
	}
	
	// Countable implementation
	
	function count() {
		return count($this->row);
	}
	
	// ArrayAccess implementation
	
	/** Test if column exists
	* @param string column name
	* @return bool
	*/
	function offsetExists($key) {
		$this->access($key);
		$return = array_key_exists($key, $this->row);
		if (!$return) {
			$this->access($key, true);
		}
		return $return;
	}
	
	/** Get value of column
	* @param string column name
	* @return string
	*/
	function offsetGet($key) {
		$this->access($key);
		if (!array_key_exists($key, $this->row)) {
			$this->access($key, true);
		}
		return $this->row[$key];
	}
	
	/** Store value in column
	* @param string column name
	* @return null
	*/
	function offsetSet($key, $value) {
		$this->row[$key] = $value;
		$this->modified[$key] = $value;
	}
	
	/** Remove column from data
	* @param string column name
	* @return null
	*/
	function offsetUnset($key) {
		unset($this->row[$key]);
		unset($this->modified[$key]);
	}
	
	// JsonSerializable implementation
	
	function jsonSerialize() {
		return $this->row;
	}

    // @dogstar 2014-10-24
    function toArray()
    {
        return $this->row();
    }
	
}
