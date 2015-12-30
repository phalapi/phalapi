<?php
/**
 * PhalApi_Config_Yaconf Yaconf扩展配置类
 *
 * - 通过Yaconf扩展快速获取配置
 *
 * 使用示例：
```
 * <code>
 * $config = new PhalApi_Config_Yaconf();
 *
 * var_dump($config->get('foo')); //相当于var_dump(Yaconf::get("foo"));
 *
 * var_dump($config->has('foo')); //相当于var_dump(Yaconf::has("foo"));
 * </code>
```
 *
 * @package     PhalApi\Config
 * @see         PhalApi_Config::get()
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @link        https://github.com/laruence/yaconf
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Config_Yaconf implements PhalApi_Config {

    public function get($key, $default = NULL) {
        return Yaconf::get($key, $default);
    }

    public function has($key) {
        return Yaconf::has($key);
    }
}
