<?php

/** Loading and saving data, it's only cache so load() does not need to block until save()
*/
interface NotORM_Cache {
	
	/** Load stored data
	* @param string
	* @return mixed or null if not found
	*/
	function load($key);
	
	/** Save data
	* @param string
	* @param mixed
	* @return null
	*/
	function save($key, $data);
	
}



/** Cache using $_SESSION["NotORM"]
*/
class NotORM_Cache_Session implements NotORM_Cache {
	
	function load($key) {
		if (!isset($_SESSION["NotORM"][$key])) {
			return null;
		}
		return $_SESSION["NotORM"][$key];
	}
	
	function save($key, $data) {
		$_SESSION["NotORM"][$key] = $data;
	}
	
}



/** Cache using file
*/
class NotORM_Cache_File implements NotORM_Cache {
	private $filename, $data = array();
	
	function __construct($filename) {
		$this->filename = $filename;
		$this->data = unserialize(@file_get_contents($filename)); // @ - file may not exist
	}
	
	function load($key) {
		if (!isset($this->data[$key])) {
			return null;
		}
		return $this->data[$key];
	}
	
	function save($key, $data) {
		if (!isset($this->data[$key]) || $this->data[$key] !== $data) {
			$this->data[$key] = $data;
			file_put_contents($this->filename, serialize($this->data), LOCK_EX);
		}
	}
	
}



/** Cache using PHP include
*/
class NotORM_Cache_Include implements NotORM_Cache {
	private $filename, $data = array();
	
	function __construct($filename) {
		$this->filename = $filename;
		$this->data = @include realpath($filename); // @ - file may not exist, realpath() to not include from include_path //! silently falls with syntax error and fails with unreadable file
		if (!is_array($this->data)) { // empty file returns 1
			$this->data = array();
		}
	}
	
	function load($key) {
		if (!isset($this->data[$key])) {
			return null;
		}
		return $this->data[$key];
	}
	
	function save($key, $data) {
		if (!isset($this->data[$key]) || $this->data[$key] !== $data) {
			$this->data[$key] = $data;
			file_put_contents($this->filename, '<?php return ' . var_export($this->data, true) . ';', LOCK_EX);
		}
	}
	
}



/** Cache storing data to the "notorm" table in database
*/
class NotORM_Cache_Database implements NotORM_Cache {
	private $connection;
	
	function __construct(PDO $connection) {
		$this->connection = $connection;
	}
	
	function load($key) {
		$result = $this->connection->prepare("SELECT data FROM notorm WHERE id = ?");
		$result->execute(array($key));
		$return = $result->fetchColumn();
		if (!$return) {
			return null;
		}
		return unserialize($return);
	}
	
	function save($key, $data) {
		// REPLACE is not supported by PostgreSQL and MS SQL
		$parameters = array(serialize($data), $key);
		$result = $this->connection->prepare("UPDATE notorm SET data = ? WHERE id = ?");
		$result->execute($parameters);
		if (!$result->rowCount()) {
			$result = $this->connection->prepare("INSERT INTO notorm (data, id) VALUES (?, ?)");
			try {
				@$result->execute($parameters); // @ - ignore duplicate key error
			} catch (PDOException $e) {
				if ($e->getCode() != "23000") { // "23000" - duplicate key
					throw $e;
				}
			}
		}
	}
	
}



// eAccelerator - user cache is obsoleted



/** Cache using "NotORM." prefix in Memcache
*/
class NotORM_Cache_Memcache implements NotORM_Cache {
	private $memcache;
	
	function __construct(Memcache $memcache) {
		$this->memcache = $memcache;
	}
	
	function load($key) {
		$return = $this->memcache->get("NotORM.$key");
		if ($return === false) {
			return null;
		}
		return $return;
	}
	
	function save($key, $data) {
		$this->memcache->set("NotORM.$key", $data);
	}
	
}



/** Cache using "NotORM." prefix in APC
*/
class NotORM_Cache_APC implements NotORM_Cache {
	
	function load($key) {
		$success = false;
		$return = apc_fetch("NotORM.$key", $success);
		if (!$success) {
			return null;
		}
		return $return;
	}
	
	function save($key, $data) {
		apc_store("NotORM.$key", $data);
	}
	
}
