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
 * Dependence Injection Class
 *
 *  Dependency Injection Container
 *  
 * - support: setter/getter, magic method like setX/getX, class property like $di->X, array like $di['X']
 * - ways to initialize: assign directly, or by class name, or by anonymous function
 *
 * <br>Usage:<br>
```
 *       $di = new PhalApi_DI();
 *      
 *       // setter/getter, magic method like setX/getX, class property like $di->X, array like $di['X']
 *       $di->key = 'value';
 *       $di['key'] = 'value';
 *       $di->set('key', 'value');
 *       $di->setKey('value');
 *      
 *       echo $di->key;
 *       echo $di['key'];
 *       echo $di->get('key');
 *       echo $di->getKey();
 *      
 *       // ways to initialize: assign directly, or by class name(will call onInitialize method), or by anonymous function
 *       $di->simpleKey = array('value');
 *       $di->classKey = 'PhalApi_DI';
 *       $di->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
```       
 *      
 * Default Services:     
 *      
 * @property PhalApi_Request        $request    request
 * @property PhalApi_Response_Json  $response   reponse
 * @property PhalApi_Cache          $cache      cache
 * @property PhalApi_Crypt          $crypt      crypt
 * @property PhalApi_Config         $config     config
 * @property PhalApi_Logger         $logger     logger
 * @property PhalApi_DB_NotORM      $notorm     NotORM
 * @property PhalApi_Loader         $loader     loader
 * 
 * @package     PhalApi\DI
 * @link        http://docs.phalconphp.com/en/latest/reference/di.html
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-01-22
 */ 

class PhalApi_DI implements ArrayAccess {

	/**
	 * @var 	PhalApi_DI 		$instance 	singleton object
	 */
    protected static $instance = NULL;

    /**
     * @var 	array 			$hitTimes 	services hit times
     */
    protected $hitTimes = array();
    
    /**
     * @var 	array 			$data		services regsitration pool
     */
    protected $data = array();

    public function __construct() {

    }

    /**
     * Get the singletom of DI
     *
     * - 1. construct and initialize default services such as reqeust, response
     * - 2. you can also create by new, but can not share servcies
     */ 
    public static function one() {
        if (self::$instance == NULL) {
            self::$instance = new PhalApi_DI();
            self::$instance->onConstruct();
        }

        return self::$instance;
    }

    /**
     * Construct on services
     *
     * - 1. custom specified operations on your logic business, e.g. add default services
     * - 2. only will triggle at first time
     */ 
    public function onConstruct() {
        $this->request = 'PhalApi_Request';
        $this->response = 'PhalApi_Response_Json';
    }

    public function onInitialize() {
    }

    /**
     * Setter
     *
     * - 1. save meta constructor, and initilize when in need (lazy initialization)
     *
     * @param 		string 		$key 		service name, unique, case sensitive
     * @param 		mixed 		$value 		service value, it could by some value, instance, class name, or anonymous function
     */ 
    public function set($key, $value) {
        $this->resetHit($key);

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Getter
     *
     * - 1. get the value of service, and initiliaze for different cases
     * - 2. try to call onConstruct() when first time to create
     *
     * @param 		string 		$key 		service name, unique, case sensitive
     * @param 		mixed 		$default 	service default value
     * @return 		mixed 					return NULL when service not exists
     */ 
    public function get($key, $default = NULL) {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        $this->recordHitTimes($key);

        if ($this->isFirstHit($key)) {
            $this->data[$key] = $this->initService($this->data[$key]);
        }

        return $this->data[$key];
    }

    /** ------------------ Magic Methods ------------------ **/

    public function __call($name, $arguments) {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : NULL);
        } else if (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : NULL);
        } else {
        }

        throw new PhalApi_Exception_InternalServerError(
            T('Call to undefined method PhalApi_DI::{name}() .', array('name' => $name))
        );
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __get($name) {
        return $this->get($name, NULL);
    }

    /** ------------------ ArrayAccess Interfaces ------------------ **/

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetGet($offset) {
        return $this->get($offset, NULL);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /** ------------------ Potected Methods ------------------ **/

    protected function initService($config) {
        $rs = NULL;

        if ($config instanceOf Closure) {
            $rs = $config();
        } elseif (is_string($config) && class_exists($config)) {
            $rs = new $config();
            if(is_callable(array($rs, 'onInitialize'))) {
                call_user_func(array($rs, 'onInitialize'));
            }
        } else {
            $rs = $config;
        }

        return $rs;
    }

    protected function resetHit($key) {
        $this->hitTimes[$key] = 0;
    }

    protected function isFirstHit($key) {
        return $this->hitTimes[$key] == 1;
    }

    protected function recordHitTimes($key) {
        if (!isset($this->hitTimes[$key])) {
            $this->hitTimes[$key] = 0;
        }

        $this->hitTimes[$key] ++;
    }
}

