<?php
namespace PhalApi;

/**
 * Filter 拦截器接口
 *  
 * 为应用实现接口请求拦截提供统一处理接口
 * 
 * <br>实现和使用示例：</br>
```
 * 	class MyFilter implements Filter {
 * 
 * 		public function check() {
 * 			//TODO
 * 		}
 * 	}
 *
 * //$ vim ./Public/init.php
 * //注册签名验证服务 
 * DI()->filter = 'MyFilter';
```
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-25
 */

interface Filter {

    public function check();
}
