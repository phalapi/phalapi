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
 * PhalApi Api Class
 *
 * - 
 * - including Authentication, creating API params by rules, etc.
 * - project API should extend this base API class
 *
 * <br>Generally, class can extend PhalApi_Api like:<br>
 *
```
 *  class Api_Demo extends PhalApi_Api {
 *      
 *      public function getRules() {
 *          return array(
 *              // ...
 *          );
 *      }
 *
 *      public function doSth() {
 *          $rs = array();
 *
 *          // ...
 *
 *          return $rs;
 *      }
 *  }
```
 *
 * @property    mixed $whatever API parmas
 * @package     PhalApi\Api
 * @license     http://www.phalapi.net/license GPL GPL License GPL GPL License License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Api {

    /**
     * Set API param parsed from request with rules
     * 
     * @param 	string 	$name 	API param name
     * @param 	mixed 	$value 	API param value after parsed
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Get API param parsed from request with rules
     * 
     * @param 	string 	$name 	API param name
     * @throws 	PhalApi_Exception_InternalServerError throw 500 when try to get undefined API param
     * @return 	mixed
     */
    public function __get($name) {
        if(!isset($this->$name) || empty($name)) {
            throw new PhalApi_Exception_InternalServerError(
                T('PhalApi_Api::${name} undefined', array('name' => $name))
            );
        }

        return $this->$name;
    }

    /**
     * Initialization
     *
     * Initialization is mainly composed of:
     * - 1. [Required]parse and generate API params by rules
     * - 2. [Optional]exec filter, eg: signature verification
     * - 3. [Optional]user authentication
     * 
     * @uses 	PhalApi_Api::createMemberValue()
     * @uses 	PhalApi_Api::filterCheck()
     * @uses 	PhalApi_Api::userCheck()
     * @return 	null
     */
    public function init() {
        $this->createMemberValue();

        $this->filterCheck();

        $this->userCheck();
    }

    /**
     * Parse and generate API params by rules
     *
     * - according the config of params rules, generate API param and save into class member after parse
     * 
     * @uses 	PhalApi_Api::getApiRules()
     */
    protected function createMemberValue() {
        foreach ($this->getApiRules() as $key => $rule) {
            $this->$key = DI()->request->getByRule($rule);
        }
    }

    /**
     * Get all API rules
     *
     * mainly composed of:
     * - 1. [Fixed]the only one system level param, that's `service` param
     * - 2. application level common API rules in configuration `app.apiCommonRules`
     * - 3. API level common params rules in the configuration `*` of API class
     * - 4. API level specified params rules
     *
     * <b>NOTE: The priority of rules: 1 < 2 < 3 < 4. Otherwise both request method name and config indexes will trans into lowercase before being compared with others. </b>
     *
     * @uses 	PhalApi_Api::getRules()
     * @return 	array
     */
    public function getApiRules() {
        $rules = array();

        $allRules = $this->getRules();
        if (!is_array($allRules)) {
            $allRules = array();
        }

        $allRules = array_change_key_case($allRules, CASE_LOWER);

        $service = DI()->request->get('service', 'Default.Index');
        list($apiClassName, $action) = explode('.', $service);
        $action = strtolower($action); 

        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }
        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        $apiCommonRules = DI()->config->get('app.apiCommonRules', array());
        if (!empty($apiCommonRules) && is_array($apiCommonRules)) {
            $rules = array_merge($apiCommonRules, $rules);
        }

        return $rules;
    }

    /**
     * Get the rules for parmas
     *
     * - developer can override it as needed
     * 
     * @return 	array
     */
    public function getRules() {
        return array();
    }

    /**
     * Exec filter
     *
     * - developer can override it as needed, in order to implemnet filter something, you should:
     * - 1. implemnet interface PhalApi_Filter::check()
     * - 2. register DI()->filter
     *
     * <br>This is a simple example as below:<br>
```
     * 	class My_Filter implements PhalApi_Filter {
     * 
     * 		public function check() {
     * 			//TODO
     * 		}
     * 	}
     * 
     * 
     *  // register DI()->filter in the file init.php
     *  DI()->filter = 'My_Filter';
```
     * 
     * @see 	PhalApi_Filter::check()
     * @throws 	PhalApi_Exception_BadRequest throw 400 exception when fail to check
     */
    protected function filterCheck() {
        $filter = DI()->get('filter', 'PhalApi_Filter_None');

        if (isset($filter)) {
            if (!($filter instanceof PhalApi_Filter)) {
                throw new PhalApi_Exception_InternalServerError(
                    T('DI()->filter should be instanceof PhalApi_Filter'));
            }

            $filter->check();
        }
    }

    /**
     * User authentication
     *
     * - developer can override it as needed. Generally, this implementation can be delegated or implement in API base class
     * 
     * @throws 	PhalApi_Exception_BadRequest throw 400 exception when fail to check
     */
    protected function userCheck() {

    }

}
