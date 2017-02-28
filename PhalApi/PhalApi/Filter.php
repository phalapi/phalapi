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
 * Filter Class
 *  
 * Union filter for API request
 * 
 * <br>Implementaion and usage：</br>
```
 * 	class My_Filter implements PhalApi_Filter {
 * 
 * 		public function check() {
 * 			//TODO
 * 		}
 * 	}
 *
 * // $ vim ./Public/init.php
 * // registger filter service 
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
