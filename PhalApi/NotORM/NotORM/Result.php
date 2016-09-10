<?php

/** Filtered table representation
 * @method NotORM_Result and (mixed $condition, mixed $parameters = array()) Add AND condition
 * @method NotORM_Result or (mixed $condition, mixed $parameters = array()) Add OR condition
 */
class NotORM_Result extends NotORM_Abstract implements Iterator, ArrayAccess, Countable, JsonSerializable{
    protected $single;
    protected $select             = array(), $conditions = array(), $where = array(), $parameters = array(), $order = array(), $limit = null, $offset = null, $group = "", $having = "", $lock = null;
    protected $union              = array(), $unionOrder = array(), $unionLimit = null, $unionOffset = null;
    protected $data, $referencing = array(), $aggregation = array(), $accessed, $access, $keys = array();

    public static $queryTimes = 0;

    /** Create table result
     *
     * @param string
     * @param NotORM
     * @param bool single row
     *
     * @access protected must be public because it is called from NotORM
     */
    function __construct($table, NotORM $notORM, $single = false){
        $this->table   = $table;
        $this->notORM  = $notORM;
        $this->single  = $single;
        $this->primary = $notORM->structure->getPrimary($table);
    }

    /** Save data to cache and empty result
     */
    function __destruct(){
        if($this->notORM->cache && !$this->select && isset($this->rows)){
            $access = $this->access;
            if(is_array($access)){
                $access = array_filter($access);
            }
            $this->notORM->cache->save("$this->table;" . implode(",", $this->conditions), $access);
        }
        $this->rows = null;
        unset($this->data);
    }

    protected function limitString($limit, $offset = null){
        $return = "";
        if(isset($limit) && $this->notORM->driver != "oci" && $this->notORM->driver != "dblib" && $this->notORM->driver != "mssql" && $this->notORM->driver != "sqlsrv"){
            $return .= " LIMIT $limit";
            if($offset){
                //$return .= " OFFSET $offset";
                $return .= ",$offset";      //@dogstar 2014-10-24
            }
        }
        return $return;
    }

    protected function removeExtraDots($expression){
        return preg_replace('~(?:\\b[a-z_][a-z0-9_.:]*[.:])?([a-z_][a-z0-9_]*)[.:]([a-z_*])~i', '\\1.\\2', $expression); // rewrite tab1.tab2.col
    }

    protected function whereString(){
        $return = "";
        if($this->group){
            $return .= " GROUP BY $this->group";
        }
        if($this->having){
            $return .= " HAVING $this->having";
        }
        if($this->order){
            $return .= " ORDER BY " . implode(", ", $this->order);
        }
        $return = $this->removeExtraDots($return);

        $where = $this->where;
        if(isset($this->limit) && $this->notORM->driver == "oci"){
            $where[] = ($where ? " AND " : "") . "(" . ($this->offset ? "rownum > $this->offset AND "
                    : "") . "rownum <= " . ($this->limit + $this->offset) . ")"; //! rownum > doesn't work - requires subselect (see adminer/drivers/oracle.inc.php)
        }
        if($where){
            $return = " WHERE " . implode($where) . $return;
        }

        $return .= $this->limitString($this->limit, $this->offset);
        if(isset($this->lock)){
            $return .= ($this->lock ? " FOR UPDATE" : " LOCK IN SHARE MODE");
        }
        return $return;
    }

    protected function topString($limit, $offset = null){
        if(isset($limit) && ($this->notORM->driver == "dblib" || $this->notORM->driver == "mssql" || $this->notORM->driver == "sqlsrv")){
            return " TOP ($this->limit)"; //! offset is not supported
        }
        return "";
    }

    protected function createJoins($val){
        $return = array();
        preg_match_all('~\\b([a-z_][a-z0-9_.:]*[.:])[a-z_*]~i', $val, $matches);
        foreach($matches[1] as $names){
            $parent = $this->table;
            if($names != "$parent."){ // case-sensitive
                preg_match_all('~\\b([a-z_][a-z0-9_]*)([.:])~i', $names, $matches, PREG_SET_ORDER);
                foreach($matches as $match){
                    list(, $name, $delimiter) = $match;
                    $table         = $this->notORM->structure->getReferencedTable($name, $parent);
                    $column        = ($delimiter == ':' ? $this->notORM->structure->getPrimary($parent)
                        : $this->notORM->structure->getReferencedColumn($name, $parent));
                    $primary       = ($delimiter == ':' ? $this->notORM->structure->getReferencedColumn($parent, $table)
                        : $this->notORM->structure->getPrimary($table));
                    $return[$name] = " LEFT JOIN $table" . ($table != $name ? " AS $name"
                            : "") . " ON $parent.$column = $name.$primary"; // should use alias if the table is used on more places
                    $parent        = $name;
                }
            }
        }
        return $return;
    }

    /** Get SQL query
     * @return string
     */
    function __toString(){
        $return = "SELECT" . $this->topString($this->limit, $this->offset) . " ";
        $join   = $this->createJoins(implode(",", $this->conditions) . "," . implode(",", $this->select) . ",$this->group,$this->having," . implode(",", $this->order));
        if(!isset($this->rows) && $this->notORM->cache && !is_string($this->accessed)){
            $this->accessed = $this->notORM->cache->load("$this->table;" . implode(",", $this->conditions));
            $this->access   = $this->accessed;
        }
        if($this->select){
            $return .= $this->removeExtraDots(implode(", ", $this->select));
        }elseif($this->accessed){
            $return .= ($join ? "$this->table." : "") . implode(", " . ($join ? "$this->table."
                        : ""), array_keys($this->accessed));
        }else{
            $return .= ($join ? "$this->table." : "") . "*";
        }
        $return .= " FROM $this->table" . implode($join) . $this->whereString();
        if($this->union){
            $return = ($this->notORM->driver == "sqlite" || $this->notORM->driver == "oci" ? $return
                    : "($return)") . implode($this->union);
            if($this->unionOrder){
                $return .= " ORDER BY " . implode(", ", $this->unionOrder);
            }
            $return .= $this->limitString($this->unionLimit, $this->unionOffset);
            $top = $this->topString($this->unionLimit, $this->unionOffset);
            if($top){
                $return = "SELECT$top * FROM ($return) t";
            }
        }
        return $return;
    }

    /**
     * 放开限制 protected -> public @scott 反馈
     */
    public function query($query, $parameters){
        $debugTrace = array();

        self::$queryTimes++;

        /**
         * 修正当参数过多时的SQLSTATE[HY093] @dogstar 2014-11-18
         */
        $parameters = array_map(array($this, 'formatValue'), $parameters);
        foreach($parameters as $key => $val){
            if(substr($key, 0, 1) == ':' && stripos($query, $key) === false){
                unset($parameters[$key]);
            }
        }

        if($this->notORM->debug){
            $debugTrace['startTime'] = microtime(true);

            if(!is_callable($this->notORM->debug)){
                $debug = "$query;";
                if($parameters){
                    $debug .= " -- " . implode(", ", array_map(array($this, 'quote'), $parameters));
                }
                $pattern = '(^' . preg_quote(dirname(__FILE__)) . '(\\.php$|[/\\\\]))'; // can be static
                foreach(debug_backtrace() as $backtrace){
                    if(isset($backtrace["file"]) && !preg_match($pattern, $backtrace["file"])){ // stop on first file outside NotORM source codes
                        break;
                    }
                }
                error_log("{$backtrace['file']}:{$backtrace['line']}:$debug\n", 0);
                //if ($this->notORM->debug) echo "$debug<br />\n";    //@dogstar 2014-10-31

                $debugTrace['sql'] = $debug;
            }elseif(call_user_func($this->notORM->debug, $query, $parameters) === false){
                return false;
            }
        }

        $return = $this->notORM->connection->prepare($query);

        //对于替换参数进行处理   @喵了个咪 2015-12-25
        $sum = 1;
        foreach($parameters as $key => $val){
            if(strstr($key, ':')){
                if(is_int($val)){
                    $return->bindValue($key, $val, PDO::PARAM_INT);
                }elseif(is_bool($val)){
                    $return->bindValue($key, $val, PDO::PARAM_BOOL);
                }elseif(is_null($val)){
                    $return->bindValue($key, $val, PDO::PARAM_NULL);
                }else{
                    $return->bindValue($key, $val);
                }
            }else{
                if(is_int($val)){
                    $return->bindValue($sum, $val, PDO::PARAM_INT);
                }elseif(is_bool($val)){
                    $return->bindValue($sum, $val, PDO::PARAM_BOOL);
                }elseif(is_null($val)){
                    $return->bindValue($sum, $val, PDO::PARAM_NULL);
                }else{
                    $return->bindValue($sum, $val);
                }
                $sum++;
            }
        }

        if(!$return || !$return->execute()){

            $errorInfo = $return->errorInfo();

            $errorMessage = isset($errorInfo[2]) ? $errorInfo[2] : $errorMessage;

            $return = false;
        }

        if($this->notORM->debug){
            $debugTrace['endTime'] = microtime(true);

            echo sprintf("[%s - %ss]%s<br>\n", self::$queryTimes, round($debugTrace['endTime'] - $debugTrace['startTime'], 5), $debugTrace['sql']);
        }

        //显式抛出异常，以让开发同学尽早发现SQL语法问题 @dogstar 20150426
        if($return === false && $errorMessage !== null){
            throw new PDOException($errorMessage);
        }

        if($this->notORM->debugTimer){
            call_user_func($this->notORM->debugTimer);
        }

        return $return;
    }

    protected function formatValue($val){
        if($val instanceof DateTime){
            return $val->format("Y-m-d H:i:s"); //! may be driver specific
        }
        return $val;
    }

    protected function quote($val){
        if(!isset($val)){
            return "NULL";
        }
        if(is_array($val)){ // (a, b) IN ((1, 2), (3, 4))
            return "(" . implode(", ", array_map(array($this, 'quote'), $val)) . ")";
        }
        $val = $this->formatValue($val);
        if(is_float($val)){
            return sprintf("%F", $val); // otherwise depends on setlocale()
        }
        if($val === false){
            return "0";
        }
        if(is_int($val) || $val instanceof NotORM_Literal){ // number or SQL code - for example "NOW()"
            return (string) $val;
        }
        return $this->notORM->connection->quote($val);
    }

    /** Shortcut for call_user_func_array(array($this, 'insert'), $rows)
     *
     * @param array
     *
     * @return int number of affected rows or false in case of an error
     */
    function insert_multi(array $rows){
        if($this->notORM->freeze){
            return false;
        }
        if(!$rows){
            return 0;
        }
        $data       = reset($rows);
        $parameters = array();
        if($data instanceof NotORM_Result){
            $parameters = $data->parameters; //! other parameters
            $data       = (string) $data;
        }elseif($data instanceof Traversable){
            $data = iterator_to_array($data);
        }
        $insert = $data;
        if(is_array($data)){
            $values = array();
            foreach($rows as $value){
                if($value instanceof Traversable){
                    $value = iterator_to_array($value);
                }
                $values[] = $this->quote($value);
                foreach($value as $val){
                    if($val instanceof NotORM_Literal && $val->parameters){
                        $parameters = array_merge($parameters, $val->parameters);
                    }
                }
            }
            //! driver specific extended insert
            $insert = ($data || $this->notORM->driver == "mysql"
                ? "(`" . implode("`, `", array_keys($data)) . "`) VALUES " . implode(", ", $values) : "DEFAULT VALUES");
        }
        // requires empty $this->parameters
        $return = $this->query("INSERT INTO $this->table $insert", $parameters);
        if(!$return){
            return false;
        }
        $this->rows = null;
        return $return->rowCount();
    }

    /** Insert row in a table
     *
     * @param mixed array($column => $value)|Traversable for single row insert or NotORM_Result|string for INSERT ... SELECT
     * @param ... used for extended insert
     *
     * @return mixed inserted NotORM_Row or false in case of an error or number of affected rows for INSERT ... SELECT
     */
    function insert($data){
        $rows   = func_get_args();
        $return = $this->insert_multi($rows);
        if(!$return){
            return false;
        }
        if(!is_array($data)){
            return $return;
        }

        // #56 postgresql无法获取新增数据的主键ID @ clov4r-连友 201608
        if ($this->notORM->driver == "pgsql") {
            if (!isset($data[$this->primary])) {
                //获取序列名称
                $pgss = $this->query("SELECT pg_get_serial_sequence('" . $this->table . "', '" . $this->primary . "') pgss", $this->parameters)->fetch();
                if (isset($pgss['pgss'])) {
                    $rs                   = $this->query("select last_value id from " . $pgss['pgss'], $this->parameters)->fetch();
                    $data[$this->primary] = $rs['id'];
                    $this->sequence       = $rs['id'];
                }
            }
        } else {
            if (!isset($data[$this->primary]) && ($id = $this->notORM->connection->lastInsertId($this->notORM->structure->getSequence($this->table)))) {
                $data[$this->primary] = $id;
            }
        }
        return new $this->notORM->rowClass($data, $this);
    }

    /** Update all rows in result set
     *
     * @param array ($column => $value)
     *
     * @return int number of affected rows or false in case of an error
     */
    function update(array $data){
        if($this->notORM->freeze){
            return false;
        }
        if(!$data){
            return 0;
        }
        $values     = array();
        $parameters = array();
        foreach($data as $key => $val){
            // doesn't use binding because $this->parameters can be filled by ? or :name
            $values[] = "$key = " . $this->quote($val);
            if($val instanceof NotORM_Literal && $val->parameters){
                $parameters = array_merge($parameters, $val->parameters);
            }
        }
        if($this->parameters){
            $parameters = array_merge($parameters, $this->parameters);
        }
        // joins in UPDATE are supported only in MySQL
        $return = $this->query("UPDATE" . $this->topString($this->limit, $this->offset) . " $this->table SET " . implode(", ", $values) . $this->whereString(), $parameters);
        if(!$return){
            return false;
        }
        return $return->rowCount();
    }

    /** Insert row or update if it already exists
     *
     * @param array ($column => $value)
     * @param array ($column => $value)
     * @param array ($column => $value), empty array means use $insert
     *
     * @return int number of affected rows or false in case of an error
     */
    function insert_update(array $unique, array $insert, array $update = array()){
        if(!$update){
            $update = $insert;
        }
        $insert = $unique + $insert;
        $values = "(" . implode(", ", array_keys($insert)) . ") VALUES " . $this->quote($insert);
        //! parameters
        if($this->notORM->driver == "mysql"){
            $set = array();
            if(!$update){
                $update = $unique;
            }
            foreach($update as $key => $val){
                $set[] = "$key = " . $this->quote($val);
                //! parameters
            }
            return $this->insert("$values ON DUPLICATE KEY UPDATE " . implode(", ", $set));
        }else{
            $connection = $this->notORM->connection;
            $errorMode  = $connection->getAttribute(PDO::ATTR_ERRMODE);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            try{
                $return = $this->insert($values);
                $connection->setAttribute(PDO::ATTR_ERRMODE, $errorMode);
                return $return;
            }
            catch(PDOException $e){
                $connection->setAttribute(PDO::ATTR_ERRMODE, $errorMode);
                if($e->getCode() == "23000" || $e->getCode() == "23505"){ // "23000" - duplicate key, "23505" unique constraint pgsql
                    if(!$update){
                        return 0;
                    }
                    $clone  = clone $this;
                    $return = $clone->where($unique)->update($update);
                    return ($return ? $return + 1 : $return);
                }
                if($errorMode == PDO::ERRMODE_EXCEPTION){
                    throw $e;
                }elseif($errorMode == PDO::ERRMODE_WARNING){
                    trigger_error("PDOStatement::execute(): " . $e->getMessage(), E_USER_WARNING); // E_WARNING is unusable
                }
            }
        }
    }

    /** Get last insert ID
     * @return string number
     */
    function insert_id(){
        return $this->notORM->connection->lastInsertId();
    }

    /** Delete all rows in result set
     * @return int number of affected rows or false in case of an error
     */
    function delete(){
        if($this->notORM->freeze){
            return false;
        }
        //防止误删，禁止全表的删除
        //@dogstar - 2014-10-24
        $where = $this->whereString();
        if(empty($where)){
            throw new Exception('sorry, you can not delete the whole table --dogstar');
            return false;
        }

        $return = $this->query("DELETE" . $this->topString($this->limit, $this->offset) . " FROM $this->table" . $where, $this->parameters);
        if(!$return){
            return false;
        }
        return $return->rowCount();
    }

    /** Add select clause, more calls appends to the end
     *
     * @param string for example "column, MD5(column) AS column_md5", empty string to reset previously set columns
     * @param string ...
     *
     * @return NotORM_Result fluent interface
     */
    function select($columns){
        $this->__destruct();
        if($columns != ""){
            foreach(func_get_args() as $columns){
                $this->select[] = $columns;
            }
        }else{
            $this->select = array();
        }
        return $this;
    }

    /** Add where condition, more calls appends with AND
     *
     * @param mixed string possibly containing ? or :name; or array($condition => $parameters, ...)
     * @param mixed array accepted by PDOStatement::execute or a scalar value
     * @param mixed ...
     *
     * @return NotORM_Result fluent interface
     */
    function where($condition, $parameters = array()){
        $args = func_get_args();
        return $this->whereOperator("AND", $args);
    }

    protected function whereOperator($operator, array $args){
        $condition  = $args[0];
        $parameters = (count($args) > 1 ? $args[1] : array());
        if(is_array($condition)){ // where(array("column1" => 1, "column2 > ?" => 2))
            foreach($condition as $key => $val){
                $this->where($key, $val);
            }
            return $this;
        }
        $this->__destruct();
        $this->conditions[] = "$operator $condition";
        $condition          = $this->removeExtraDots($condition);
        if(count($args) != 2 || strpbrk($condition, "?:")){ // where("column < ? OR column > ?", array(1, 2))
            if(count($args) != 2 || !is_array($parameters)){ // where("column < ? OR column > ?", 1, 2)
                $parameters = array_slice($args, 1);
            }
            $this->parameters = array_merge($this->parameters, $parameters);
        }elseif($parameters === null){ // where("column", null)
            $condition .= " IS NULL";
        }elseif($parameters instanceof NotORM_Result){ // where("column", $db->$table())
            $clone = clone $parameters;
            if(!$clone->select){
                $clone->select($this->notORM->structure->getPrimary($clone->table));
            }
            if($this->notORM->driver != "mysql"){
                if($clone instanceof NotORM_MultiResult){
                    array_shift($clone->select);
                    $clone->single();
                }
                $condition .= " IN ($clone)";
                $this->parameters = array_merge($this->parameters, $clone->parameters);
            }else{
                $in = array();
                foreach($clone as $row){
                    $row = array_values(iterator_to_array($row));
                    if($clone instanceof NotORM_MultiResult && count($row) > 1){
                        array_shift($row);
                    }
                    if(count($row) == 1){
                        $in[] = $this->quote($row[0]);
                    }else{
                        $in[] = $this->quote($row);
                    }
                }
                if($in){
                    $condition .= " IN (" . implode(", ", $in) . ")";
                }else{
                    $condition = "($condition) IS NOT NULL AND $condition IS NULL"; // $condition = "NOT id"
                }
            }
        }elseif(!is_array($parameters)){ // where("column", "x")
            $condition .= " = " . $this->quote($parameters);
        }else{ // where("column", array(1, 2))
            $condition = $this->whereIn($condition, $parameters);
        }
        $this->where[] = (preg_match('~^\)+$~', $condition)
            ? $condition : ($this->where ? " $operator "
                : "") . "($condition)");
        return $this;
    }

    protected function whereIn($condition, $parameters){
        if(!$parameters){
            $condition = "($condition) IS NOT NULL AND $condition IS NULL";
        }elseif($this->notORM->driver != "oci"){
            $column = $condition;
            $condition .= " IN " . $this->quote($parameters);
            $nulls = array_filter($parameters, 'is_null');
            if($nulls){
                $condition = "$condition OR $column IS NULL";
            }
        }else{ // http://download.oracle.com/docs/cd/B19306_01/server.102/b14200/expressions014.htm
            $or = array();
            for($i = 0; $i < count($parameters); $i += 1000){
                $or[] = "$condition IN " . $this->quote(array_slice($parameters, $i, 1000));
            }
            $condition = implode(" OR ", $or);
        }
        return $condition;
    }

    function __call($name, array $args){
        $operator = strtoupper($name);
        switch($operator){
            case "AND":
            case "OR":
                return $this->whereOperator($operator, $args);
        }
        trigger_error("Call to undefined method NotORM_Result::$name()", E_USER_ERROR);
    }

    /** Shortcut for where()
     *
     * @param string
     * @param mixed
     * @param mixed ...
     *
     * @return NotORM_Result fluent interface
     */
    function __invoke($where, $parameters = array()){
        $args = func_get_args();
        return $this->whereOperator("AND", $args);
    }

    /** Add order clause, more calls appends to the end
     *
     * @param string for example "column1, column2 DESC", empty string to reset previous order
     * @param string ...
     *
     * @return NotORM_Result fluent interface
     */
    function order($columns){
        $this->rows = null;
        if($columns != ""){
            foreach(func_get_args() as $columns){
                if($this->union){
                    $this->unionOrder[] = $columns;
                }else{
                    $this->order[] = $columns;
                }
            }
        }elseif($this->union){
            $this->unionOrder = array();
        }else{
            $this->order = array();
        }
        return $this;
    }

    /**
     * 对查询进行limit操作
     * 请注意以下两种用法，与NotORM原来的定义有所区别
     * - limit(数量)
     * - limit(开始位置，数量)
     * Set limit clause, more calls rewrite old values
     *
     * @param int
     * @param int
     *
     * @return NotORM_Result fluent interface
     */
    function limit($limit, $offset = null){
        $this->rows = null;
        if($this->union){
            $this->unionLimit  = +$limit;
            $this->unionOffset = +$offset;
        }else{
            $this->limit  = +$limit;
            $this->offset = +$offset;
        }
        return $this;
    }

    /** Set group clause, more calls rewrite old values
     *
     * @param string
     * @param string
     *
     * @return NotORM_Result fluent interface
     */
    function group($columns, $having = ""){
        $this->__destruct();
        $this->group  = $columns;
        $this->having = $having;
        return $this;
    }

    /** Set select FOR UPDATE or LOCK IN SHARE MODE
     *
     * @param bool
     *
     * @return NotORM_Result fluent interface
     */
    function lock($exclusive = true){
        $this->lock = $exclusive;
        return $this;
    }

    /**
     * @param NotORM_Result
     * @param bool
     *
     * @return NotORM_Result fluent interface
     */
    function union(NotORM_Result $result, $all = false){
        $this->union[]    = " UNION " . ($all ? "ALL "
                : "") . ($this->notORM->driver == "sqlite" || $this->notORM->driver == "oci" ? $result : "($result)");
        $this->parameters = array_merge($this->parameters, $result->parameters);
        return $this;
    }

    /** Execute aggregation function
     *
     * @param string
     *
     * @return string
     */
    function aggregation($function){
        $join  = $this->createJoins(implode(",", $this->conditions) . ",$function");
        $query = "SELECT $function FROM $this->table" . implode($join);
        if($this->where){
            $query .= " WHERE " . implode($this->where);
        }
        foreach($this->query($query, $this->parameters)->fetch() as $return){
            return $return;
        }
    }

    /** Count number of rows
     *
     * @param string
     *
     * @return int
     */
    function count($column = "*"){
        return $this->aggregation("COUNT($column)");
    }

    /** Return minimum value from a column
     *
     * @param string
     *
     * @return int
     */
    function min($column){
        return $this->aggregation("MIN($column)");
    }

    /** Return maximum value from a column
     *
     * @param string
     *
     * @return int
     */
    function max($column){
        return $this->aggregation("MAX($column)");
    }

    /** Return sum of values in a column
     *
     * @param string
     *
     * @return int
     */
    function sum($column){
        return $this->aggregation("SUM($column)");
    }

    /** Execute the built query
     * @return null
     */
    protected function execute(){
        if(!isset($this->rows)){
            $result     = false;
            $exception  = null;
            $parameters = array();
            foreach(array_merge($this->select, array(
                $this,
                $this->group,
                $this->having
            ), $this->order, $this->unionOrder) as $val){
                if(($val instanceof NotORM_Literal || $val instanceof self) && $val->parameters){
                    $parameters = array_merge($parameters, $val->parameters);
                }
            }
            try{
                $result = $this->query($this->__toString(), $parameters);
            }
            catch(PDOException $exception){
                // handled later
            }
            if(!$result){
                if(!$this->select && $this->accessed){
                    $this->accessed = '';
                    $this->access   = array();
                    $result         = $this->query($this->__toString(), $parameters);
                }elseif($exception){
                    throw $exception;
                }
            }
            $this->rows = array();
            if($result){
                $result->setFetchMode(PDO::FETCH_ASSOC);
                foreach($result as $key => $row){
                    if(isset($row[$this->primary])){
                        $key = $row[$this->primary];
                        if(!is_string($this->access)){
                            $this->access[$this->primary] = true;
                        }
                    }
                    //$this->rows[$key] = new $this->notORM->rowClass($row, $this);
                    if ($this->notORM->isKeepPrimaryKeyIndex) {
                        //@dogstar 采用主键作为下标 2015-12-30
                        $this->rows[$key] = $row;
                    } else {
                        //@dogstar 改用返回数组 2014-11-01
                        $this->rows[] = $row;
                    }
                }
            }
            $this->data = $this->rows;
        }
    }

    /** Fetch next row of result
     *
     * @param string column name to return or an empty string for the whole row
     *
     * @return mixed string or null with $column, NotORM_Row without $column, false if there is no row
     */
    function fetch($column = ''){
        // no $this->select($column) because next calls can access different columns
        $this->execute();
        $return = current($this->data);
        next($this->data);
        if($return && $column != ''){
            return $return[$column];
        }
        return $return;
    }

    /**
     * fetchRow别名，等效于NotORM_Result::fetchRow()
     * @author: dogstar 2015-04-26
     */
    function fetchOne($column = ''){
        return $this->fetchRow($column);
    }

    /**
     * 只查询第一行纪录，等效于NotORM_Result::fetchOne()
     * @author: dogstar 2015-04-18
     */
    function fetchRow($column = ''){
        $this->limit(1)->execute();
        return $this->fetch($column);
    }

    /**
     * 返回全部的数据，等效于NotORM_Result::fetchRows()
     * @author: dogstar 2014-11-01
     */
    function fetchAll(){
        $this->execute();
        return $this->data;
    }

    /**
     * fetchAll别名，等效于NotORM_Result::fetchAll()
     * @author: dogstar 2015-04-26
     */
    function fetchRows(){
        return $this->fetchAll();
    }

    /**
     * queryRows别名，等效于NotORM_Result::queryRows($sql, $parmas)
     * @author: dogstar 2015-04-26
     */
    function queryAll($sql, $parmas = array()){
        return $this->queryRows($sql, $parmas);
    }

    /**
     * 根据SQL查询全部数据，等效于NotORM_Result::queryAll($sql, $parmas)
     * @return array
     * @author: dogstar 2014-11-01
     */
    function queryRows($sql, $parmas = array()){
        $result = $this->query($sql, $parmas);

        $rows = array();
        if($result){
            $result->setFetchMode(PDO::FETCH_ASSOC);
            foreach($result as $key => $row){
                if(isset($row[$this->primary])){
                    $key = $row[$this->primary];
                    if(!is_string($this->access)){
                        $this->access[$this->primary] = true;
                    }
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /** Fetch all rows as associative array
     *
     * @param string
     * @param string column name used for an array value or an empty string for the whole row
     *
     * @return array
     */
    function fetchPairs($key, $value = ''){
        $return = array();
        $clone  = clone $this;
        if($value != ""){
            $clone->select = array();
            $clone->select("$key, $value"); // MultiResult adds its column
        }elseif($clone->select){
            array_unshift($clone->select, $key);
        }else{
            $clone->select = array("$key, $this->table.*");
        }
        foreach($clone as $row){
            //$values = array_values(iterator_to_array($row));
            //bugfixed: Argument 1 passed to iterator_to_array() must implement interface Traversable, array given
            //@Scott @dogstar 20151122
            $values = ($row instanceof Traversable) ? array_values(iterator_to_array($row)) : array_values($row);
            if($value != "" && $clone instanceof NotORM_MultiResult){
                array_shift($values);
            }
            $return[(string) $values[0]] = ($value != "" ? $values[(array_key_exists(1, $values) ? 1 : 0)]
                : $row); // isset($values[1]) - fetchPairs("id", "id")
        }
        return $return;
    }

    protected function access($key, $delete = false){
        if($delete){
            if(is_array($this->access)){
                $this->access[$key] = false;
            }
            return false;
        }
        if(!isset($key)){
            $this->access = '';
        }elseif(!is_string($this->access)){
            $this->access[$key] = true;
        }
        if(!$this->select && $this->accessed && (!isset($key) || !isset($this->accessed[$key]))){
            $this->accessed = '';
            $this->rows     = null;
            return true;
        }
        return false;
    }

    protected function single(){
    }

    // Iterator implementation (not IteratorAggregate because $this->data can be changed during iteration)

    function rewind(){
        $this->execute();
        $this->keys = array_keys($this->data);
        reset($this->keys);
    }

    /** @return NotORM_Row */
    function current(){
        return $this->data[current($this->keys)];
    }

    /** @return string row ID */
    function key(){
        return current($this->keys);
    }

    function next(){
        next($this->keys);
    }

    function valid(){
        return current($this->keys) !== false;
    }

    // ArrayAccess implementation

    /** Test if row exists
     *
     * @param string row ID or array for where conditions
     *
     * @return bool
     */
    function offsetExists($key){
        $row = $this->offsetGet($key);
        return isset($row);
    }

    /** Get specified row
     *
     * @param string row ID or array for where conditions
     *
     * @return NotORM_Row or null if there is no such row
     */
    function offsetGet($key){
        if($this->single && !isset($this->data)){
            $clone = clone $this;
            if(is_array($key)){
                $clone->where($key)->limit(1);
            }else{
                $clone->where($this->primary, $key);
            }
            $return = $clone->fetch();
            if($return){
                return $return;
            }
        }else{
            $this->execute();
            if(is_array($key)){
                foreach($this->data as $row){
                    foreach($key as $k => $v){
                        if((isset($v) && $row[$k] !== null ? $row[$k] != $v : $row[$k] !== $v)){
                            continue 2;
                        }
                    }
                    return $row;
                }
            }elseif(isset($this->data[$key])){
                return $this->data[$key];
            }
        }
    }

    /** Mimic row
     *
     * @param string row ID
     * @param NotORM_Row
     *
     * @return null
     */
    function offsetSet($key, $value){
        $this->execute();
        $this->data[$key] = $value;
    }

    /** Remove row from result set
     *
     * @param string row ID
     *
     * @return null
     */
    function offsetUnset($key){
        $this->execute();
        unset($this->data[$key]);
    }

    // JsonSerializable implementation

    function jsonSerialize(){
        $this->execute();
        if($this->notORM->jsonAsArray){
            return array_values($this->data);
        }else{
            return $this->data;
        }
    }

}
