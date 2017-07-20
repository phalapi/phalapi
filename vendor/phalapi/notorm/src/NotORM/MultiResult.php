<?php

/** Representation of filtered table grouped by some column
*/
class NotORM_MultiResult extends NotORM_Result {
	private $result, $column, $active;
	
	/** @access protected must be public because it is called from Row */
	function __construct($table, NotORM_Result $result, $column, $active) {
		parent::__construct($table, $result->notORM);
		$this->result = $result;
		$this->column = $column;
		$this->active = $active;
	}
	
	/** Specify referencing column
	* @param string
	* @return NotORM_MultiResult fluent interface
	*/
	function via($column) {
		$this->column = $column;
		$this->conditions[0] = "$this->table.$column AND";
		$this->where[0] = "(" . $this->whereIn("$this->table.$column", array_keys((array) $this->result->rows)) . ")";
		return $this;
	}
	
	function insert_multi(array $rows) {
		$args = array();
		foreach ($rows as $data) {
			if ($data instanceof Traversable && !$data instanceof NotORM_Result) {
				$data = iterator_to_array($data);
			}
			if (is_array($data)) {
				$data[$this->column] = $this->active;
			}
			$args[] = $data;
		}
		return parent::insert_multi($args);
	}
	
	function insert_update(array $unique, array $insert, array $update = array()) {
		$unique[$this->column] = $this->active;
		return parent::insert_update($unique, $insert, $update);
	}
	
	protected function single() {
		$this->where[0] = "($this->column = " . $this->quote($this->active) . ")";
	}
	
	function update(array $data) {
		$where = $this->where;
		$this->single();
		$return = parent::update($data);
		$this->where = $where;
		return $return;
	}
	
	function delete() {
		$where = $this->where;
		$this->single();
		$return = parent::delete();
		$this->where = $where;
		return $return;
	}
	
	function select($columns) {
		$args = func_get_args();
		if (!$this->select) {
			array_unshift($args, "$this->table.$this->column");
		}
		return call_user_func_array(array($this, 'parent::select'), $args);
	}
	
	function order($columns) {
		if (!$this->order) { // improve index utilization
			$this->order[] = "$this->table.$this->column" . (preg_match('~\\bDESC$~i', $columns) ? " DESC" : "");
		}
		$args = func_get_args();
		return call_user_func_array(array($this, 'parent::order'), $args);
	}
	
	function aggregation($function) {
		$join = $this->createJoins(implode(",", $this->conditions) . ",$function");
		$column = ($join ? "$this->table." : "") . $this->column;
		$query = "SELECT $function, $column FROM $this->table" . implode($join);
		if ($this->where) {
			$query .= " WHERE " . implode($this->where);
		}
		$query .= " GROUP BY $column";
		$aggregation = &$this->result->aggregation[$query];
		if (!isset($aggregation)) {
			$aggregation = array();
			foreach ($this->query($query, $this->parameters) as $row) {
				$aggregation[$row[$this->column]] = $row;
			}
		}
		if (isset($aggregation[$this->active])) {
			foreach ($aggregation[$this->active] as $return) {
				return $return;
			}
		}
	}
	
	function count($column = "") {
		$return = parent::count($column);
		return (isset($return) ? $return : 0);
	}
	
	protected function execute() {
		if (!isset($this->rows)) {
			$referencing = &$this->result->referencing[$this->__toString()];
			if (!isset($referencing)) {
				if (!$this->limit || count($this->result->rows) <= 1 || $this->union) {
					parent::execute();
				} else { //! doesn't work with union
					$result = clone $this;
					$first = true;
					foreach ((array) $this->result->rows as $val) {
						if ($first) {
							$result->where[0] = "$this->column = " . $this->quote($val);
							$first = false;
						} else {
							$clone = clone $this;
							$clone->where[0] = "$this->column = " . $this->quote($val);
							$result->union($clone);
						}
					}
					$result->execute();
					$this->rows = $result->rows;
				}
				$referencing = array();
				foreach ($this->rows as $key => $row) {
					$referencing[$row[$this->column]][$key] = $row;
				}
			}
			$this->data = &$referencing[$this->active];
			if (!isset($this->data)) {
				$this->data = array();
			}
		}
	}
	
}
