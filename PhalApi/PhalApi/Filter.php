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
 * PhalApi_Filter 拦截器接口
 *  
 * 为应用实现接口请求拦截提供统一处理接口
 * 
 * <br>实现和使用示例：</br>
```
 * 	class My_Filter implements PhalApi_Filter {
 * 
 * 		public function check() {
 * 			//TODO
 * 		}
 * 	}
 *
 * //$ vim ./Public/init.php
 * //注册签名验证服务 
 * DI()->filter = 'Common_SignFilter';
```
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-25
 */

interface PhalApi_Filter {

    public function check();
}
