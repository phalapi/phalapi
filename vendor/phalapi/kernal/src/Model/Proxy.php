<?php
namespace PhalApi\Model;

use PhalApi\Cache;
use PhalApi\Cache\NoneCache;

/**
 * Proxy 模型Model代理 - 重量级数据获取的应对方案
 * 
 * - 结合缓存，进行对重量级成本高的数据进行缓冲读取
 * - 为了传递获取源数据而需要的参数，引入封装成值对象的PhalApi_ModelQuery查询对象
 * - 具体子类需要实现源数据获取、返回缓存唯一key、和返回有效期
 * - 仅在有需要的情况下，使用此Model代理
 *
 * <br>实例和使用示例：<br>
```
 * class UserBaseInfo extends Proxy {
 *
 *      protected function doGetData($query) {
 *      	$model = new Model\User();
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
 * //最终的调用
 * $query = new Query();
 * $query->id = $userId;
 * $modelProxy = new UserBaseInfo();
 * $rs = $modelProxy->getData($query);
```
 *
 * @package     PhalApi\Model
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-22
 */

abstract class Proxy {
	
	protected $cache;

	/**
	 * 为代理指定委托的缓存组件，默认情况下使用DI()->cache
	 */
	public function __construct(Cache $cache = NULL) {
		$this->cache = $cache !== NULL ? $cache : \PhalApi\DI()->cache;

		//退而求其次
		if ($this->cache === NULL) {
			$this->cache = new NoneCache();
		}
	}

	/**
	 * 获取源数据 - 模板方法
	 *
	 * @param PhalApi_ModelQuery $query 查询对象
	 * @return mixed 返回源数据，但在失败的情况下别返回NULL，否则依然会穿透到此
	 */
	public function getData(Query $query = NULL) {
		$rs = NULL;

		if ($query === NULL) {
			$query = new Query();
		}

		if ($query->readCache) {
			$rs = $this->cache->get($this->getkey($query));
			if ($rs !== NULL) {
				return $rs;
			}
		}

		// 这里，将获取耗性能的数据
		$rs = $this->doGetData($query);

		if ($query->writeCache) {
			$this->cache->set($this->getKey($query), $rs, $this->getExpire($query));
		}

		return $rs;
	}
	
	/**
	 * 获取源数据 - 具体实现
	 */
	abstract protected function doGetData($query);

	/**
	 * 返回唯一缓存key，这里将$query传入，以便同类数据根据不同的值生成不同的key
	 */
	abstract protected function getKey($query);

	/**
	 * 返回缓存有效时间，单位为：秒
	 */
	abstract protected function getExpire($query);
}
