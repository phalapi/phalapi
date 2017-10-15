<?php
namespace PhalApi;

use PhalApi\Exception\InternalServerErrorException;

/**
 * Api 接口服务基类
 *
 * - 实现身份验证、按参数规则解析生成接口参数等操作
 * - 提供给开发人员自宝义的接口服务具体类继承
 *
 * <br>通常地，可以这样继承：<br>
 *
```
 *  class MyApi extends Api {
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
 * @property    mixed $whatever 接口参数
 * @package     PhalApi\Api
 * @license     http://www.phalapi.net/license GPL 协议 GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class Api {

    /**
     * @var boolean $__apiIsServiceWhitelist 是否为白名单服务
     */
    private $__apiIsServiceWhitelist;

    /**
     * 设置规则解析后的接口参数
     * @param string $name 接口参数名字
     * @param mixed $value 接口参数解析后的值
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * 获取规则解析后的接口参数
     * @param string $name 接口参数名字
     * @throws Exception_InternalServerError 获取未设置的接口参数时，返回500
     * @return mixed
     */
    public function __get($name) {
        if(!isset($this->$name) || empty($name)) {
            throw new InternalServerErrorException(
                T('Api::${name} undefined', array('name' => $name))
            );
        }

        return $this->$name;
    }

    /**
     * 初始化
     *
     * 主要完成的初始化工作有：
     * - 1、[必须]按参数规则解析生成接口参数
     * - 2、[可选]过滤器调用，如：签名验证
     * - 3、[可选]用户身份验证
     * 
     * @uses Api::createMemberValue()
     * @uses Api::filterCheck()
     * @uses Api::userCheck()
     * @return null
     */
    public function init() {
        $this->createMemberValue();

        $this->filterCheck();

        $this->userCheck();
    }

    /**
     * 按参数规则解析生成接口参数
     *
     * 根据配置的参数规则，解析过滤，并将接口参数存放于类成员变量
     * 
     * @uses Api::getApiRules()
     */
    protected function createMemberValue() {
        foreach ($this->getApiRules() as $key => $rule) {
            $this->$key = DI()->request->getByRule($rule);
        }
    }

    /**
     * 取接口参数规则
     *
     * 主要包括有：
     * - 1、[固定]系统级的service参数
     * - 2、应用级统一接口参数规则，在app.apiCommonRules中配置
     * - 3、接口级通常参数规则，在子类的*中配置
     * - 4、接口级当前操作参数规则
     *
     * <b>当规则有冲突时，以后面为准。另外，被请求的函数名和配置的下标都转成小写再进行匹配。</b>
     *
     * @uses Api::getRules()
     * @return array
     */
    public function getApiRules() {
        $rules = array();

        $allRules = $this->getRules();
        if (!is_array($allRules)) {
            $allRules = array();
        }
        $allRules = array_change_key_case($allRules, CASE_LOWER);

        $action = strtolower(DI()->request->getServiceAction()); 
        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }

        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        $apiCommonRules = DI()->config->get('app.apiCommonRules', array());
        if (!empty($apiCommonRules) && is_array($apiCommonRules)) {
            // fixed issue #22
            if ($this->isServiceWhitelist()) {
                foreach ($apiCommonRules as &$ruleRef) {
                    $ruleRef['require'] = false;
                }
            }

            $rules = array_merge($apiCommonRules, $rules);
        }

        return $rules;
    }

    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载
     * 
     * @return array
     */
    public function getRules() {
        return array();
    }

    /**
     * 过滤器调用
     *
     * 可由开发人员根据需要重载，以实现项目拦截处理，需要：
     * - 1、实现Filter::check()接口
     * - 2、注册的过滤器到DI()->filter
     *
     * <br>以下是一个简单的示例：<br>
```
     * 	class MyFilter implements Filter {
     * 
     * 		public function check() {
     * 			//TODO
     * 		}
     * 	}
     * 
     * 
     *  //在初始化文件 init.php 中注册过滤器
     *  DI()->filter = 'MyFilter';
```
     * 
     * @see Filter::check()
     * @throws Exception_BadRequest 当验证失败时，请抛出此异常，以返回400
     */
    protected function filterCheck() {
        // 过滤服务白名单
        if ($this->isServiceWhitelist()) {
            return;
        }

        $filter = DI()->get('filter', '\\PhalApi\\Filter\\NoneFilter');

        if (isset($filter)) {
            if (!($filter instanceof Filter)) {
                throw new InternalServerErrorException(
                    T('DI()->filter should be instanceof \PhalApi\Filter'));
            }

            $filter->check();
        }
    }

    /**
     * 用户身份验证
     *
     * 可由开发人员根据需要重载，此通用操作一般可以使用委托或者放置在应用接口基类
     * 
     * @throws Exception_BadRequest 当验证失败时，请抛出此异常，以返回400
     */
    protected function userCheck() {

    }

    /**
     * 是否为白名单的服务
     *
     * @return boolean
     */
    protected function isServiceWhitelist() {
        // 缓存返回，避免重复计算
        if ($this->__apiIsServiceWhitelist !== NULL) {
            return $this->__apiIsServiceWhitelist;
        }

        $this->__apiIsServiceWhitelist = FALSE;

        $di = DI();
        $api = $di->request->getServiceApi();
        $action = $di->request->getServiceAction();
        $namespace = $di->request->getNamespace();

        // 优先命名空间的单独白名单配置，再到公共白名单配置
        $serviceWhitelist = $di->config->get('app.service_whitelist.' . $namespace);
        $serviceWhitelist = $serviceWhitelist !== NULL 
            ? $serviceWhitelist : $di->config->get('app.service_whitelist', array());

        foreach ($serviceWhitelist as $item) {
            $cfgArr = is_string($item) ? explode('.', $item) : array();
            if (count($cfgArr) < 2) {
                continue;
            }

            // 短路返回
            if ($this->equalOrIngore($api, $cfgArr[0]) && $this->equalOrIngore($action, $cfgArr[1])) {
                $this->__apiIsServiceWhitelist = TRUE;
                break;
            }
        }

        return $this->__apiIsServiceWhitelist;
    }

    /**
     * 相等或忽略
     *
     * @param string $str 等判断的字符串
     * @param string $cfg 规则配置，*号表示通配
     * @return boolean
     */
    private function equalOrIngore($str, $cfg) {
        return strcasecmp($str, $cfg) == 0 || $cfg == '*';
    }
}
