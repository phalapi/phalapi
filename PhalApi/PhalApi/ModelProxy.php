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
 * Model Proxy Class, Solution To Deal With Heaviy Data
 * 
 * - cache heaviy data to save expensive cost
 * - in order to pass required params, we introduce Value Object, i.e. ```PhalApi_ModelQuery``` query object
 * - sub-class have to implement how to get source data, returning the unique cache key, and expire time
 * - use this proxy only when you need
 *
 * <br>Implementation and usage:<br>
```
 * class ModelProxy_UserBaseInfo extends PhalApi_ModelProxy {
 *
 *      protected function doGetData($query) {
 *      	$model = new Model_User();
 *      
 *      	return $model->getByUserId($query->id);
 *      }
 *     
 *      protected function getKey($query) {
 *      	return 'userbaseinfo_' . $query->id;
 *      }
 *     
 *      protected function getExpire($query) {
 *      	return 600;
 *      }
 * }
 * 
 * // final call
 * $query = new PhalApi_ModelQuery();
 * $query->id = $userId;
 * $modelProxy = new ModelProxy_UserBaseInfo();
 * $rs = $modelProxy->getData($query);
```
 *
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

abstract class PhalApi_ModelProxy {
	
	protected $cache;

	/**
	 * @param 	PhalApi_Cache 	$cache 	specify cache service for the proxy, default is: DI()->cache
	 */
	public function __construct(PhalApi_Cache $cache = NULL) {
		$this->cache = $cache !== NULL ? $cache : DI()->cache;

		// back to default cache
		if ($this->cache === NULL) {
			$this->cache = new PhalApi_Cache_None();
		}
	}

	/**
	 * Template Method: Get the source data
	 *
	 * @param 	PhalApi_ModelQuery 	$query 	query object
	 * @return 	mixed 				source data, DO NOT return NULL when fail, otherwise still try to get source data again and again
	 */
	public function getData(PhalApi_ModelQuery $query = NULL) {
		$rs = NULL;

		if ($query === NULL) {
			$query = new PhalApi_ModelQuery();
		}

		if ($query->readCache) {
			$rs = $this->cache->get($this->getkey($query));
			if ($rs !== NULL) {
				return $rs;
			}
		}

		// HERE, try to get expensive data
		$rs = $this->doGetData($query);

		if ($query->writeCache) {
			$this->cache->set($this->getKey($query), $rs, $this->getExpire($query));
		}

		return $rs;
	}
	
	/**
	 * Implementation: Get source data
	 * 
	 * @param 	PhalApi_ModelQuery 	$query		query object
	 * @return	mixed
	 */
	abstract protected function doGetData($query);

	/**
	 * Implementation: Return unique cache key
	 * 
	 * @param 	PhalApi_ModelQuery 	$query		query object
	 * @return	String
	 */
	abstract protected function getKey($query);

	/**
	 * Implementation: Reture expire time
	 * 
	 * @param 	PhalApi_ModelQuery 	$query		query object
	 * @return	Int		unit: second
	 */
	abstract protected function getExpire($query);
}
