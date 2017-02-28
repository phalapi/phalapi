<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * Model Query Class
 *
 * - we strong recommend treat this class instance as value object, even though you can use public class properties
 * - if need to copy the object, you can use constructor and ```toArray()```
 * 
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

class PhalApi_ModelQuery {

    /**
     * @var 	boolean 	$readCache 		whether read cache or not
     */
    public $readCache = true;

    /**
     * @var 	boolean 	$writeCache 	whether write cache or not
     */
    public $writeCache = true;

    /**
     * @var 	string/int 	ID
     */
    public $id;

    /**
     * @var 	int 		$timestamp 		expire timestamp
     */
    public $timestamp;

    public function __construct($queryArr = array()) {
        $this->timestamp = $_SERVER['REQUEST_TIME'];

        if (DI()->debug) {
            $this->readCache = FALSE;
            $this->writeCache = FALSE;
        }

        foreach ($queryArr as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return NULL;
    }

    public function toArray() {
        return get_object_vars($this);
    }
}
