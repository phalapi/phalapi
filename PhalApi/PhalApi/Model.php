<?php
/**
 * PhalApi_Model模型接口
 *
 * - 提供基于“表数据入口”模式的数据库接口
 * - 提供基于主键(id, etc)的基本操作，其中应对CLOB的ext_data字段的转换
 * - 为了支持数据库分表存储，实现类应该根据数据库配置读取相应的表名
 *
 * <br>使用示例：<br>
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
 * 	// 获取
 *  $rs = $model->get($userId);
 *  
 *  // 插入
 *  $model->insert(array('name' => 'whatever', 'from' => 'somewhere'));
 *  
 *  // 更新
 *  $model->update(1, array('name' => 'dogstar huang'));
 *  
 *  // 删除
 *  $model->delete(1);
```
 * 
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

interface PhalApi_Model {
	
	/**
	 * 根据主键读取纪录
	 * 
	 * @param long $id 纪录主键
	 * @param string/array $fields 需要获取的表字段，可以为字符串(如：name,from)或数组(如：array('name', 'from'))
	 * @return array 数据库表纪录
	 */
	public function get($id, $fields = '*');

	/**
	 * 插入新纪录
	 * 这里看起来有点奇怪，但如果我们需要进行分表存储，这里的参考主键是需要的
	 * 
	 * @param array $data 待插入的数据，可以包括ext_data字段
	 * @param $id 分表参考主键
	 * @return long 新插入纪录的主键值
	 */
	public function insert($data, $id = NULL);

	/**
	 * 根据主键更新纪录
	 *
	 * @param long $id 主键
	 * @param array $data 待更新的数据，可以包括ext_data字段
	 * @return TRUE/FALSE
	 */
	public function update($id, $data);

	/**
	 * 根据主键删除纪录
	 */
	public function delete($id);
}
