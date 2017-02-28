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
 * Model Class
 *
 * - provides database interface based on "Table Data Entrance"
 * - provides base operations base on primary key(eg. id), and should translate the CLOB filed ext_data
 * - in order to support multi tables, implemetation sub-class should use related table name by config
 *
 * <br>Usage:<br>
```
 * 	class Model_User extends PhalApi_Model_NotORM {
 * 
 * 		protected function getTableName($id) {
 * 			return 'user';
 * 		}
 * 	}
 * 
 * 	$model = new Model_User();
 * 
 * 	// retrieve
 *  $rs = $model->get($userId);
 *  
 *  // insert
 *  $model->insert(array('name' => 'whatever', 'from' => 'somewhere'));
 *  
 *  // update
 *  $model->update(1, array('name' => 'dogstar huang'));
 *  
 *  // delete
 *  $model->delete(1);
```
 * 
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

interface PhalApi_Model {
	
	/**
	 * Retrieve record by primary key
	 * 
	 * @param 	long 			$id 		primary key
	 * @param 	string/array 	$fields 	the fileds to be retrieved, such as: ```name,from``` in string, or: ```array('name', 'from')``` in array
	 * @return 	array 			table record, or return false when not found
	 */
	public function get($id, $fields = '*');

	/**
	 * Insert new record
	 * 
	 * it seems bo be a little strange, but the foreign key $id is required if we want to save data into multi tables
	 * 
	 * @param 	array 			$data 		data to be inserted, including the filed ```ext_data```
	 * @param 	long			$id 		foreign key
	 * @return 	long 			the id of new record
	 */
	public function insert($data, $id = NULL);

	/**
	 * Update record by primary key
	 *
	 * @param 	long 			$id 		primary key
	 * @param 	array 			$data 		data to be updated, including the filed ```ext_data```
	 * @return 	TRUE/FALSE
	 */
	public function update($id, $data);

	/**
	 * Delete record by primary key
	 * 
	 * @param 	long 			$id 		primary key
	 * @return 	TRUE/FALSE
	 */
	public function delete($id);
}
