<?php
/**
 * PhalApi_Api 接口服务基类
 *
 * - 实现身份验证、按参数规则解析生成接口参数等操作
 * - 提供给开发人员自宝义的接口服务具体类继承
 *
 * 通常地，可以这样继承：
 *
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
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Api {

    public function __set($name, $value) {
    	$this->$name = $value;
    }
    
    public function __get($name) {
    	if(!isset($this->name) || empty($name)) {
            throw new PhalApi_Exception_InternalServerError(
                T('PhalApi_Api::${name} undefined', array('name' => $name))
            );
        }

    	return $this->$name;
    }

    /**
     * 初始化
     *
     * 主要完成的初始化工作有：
     * 1、[必须]按参数规则解析生成接口参数
     * 2、[可选]过滤器调用，如：签名验证
     * 3、[可选]用户身份验证
     * 
     * @see: PhalApi_Api::createMemberValue()
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
     * 1、[固定]系统级的service参数
     * 2、应用级统一接口参数规则
     * 3、接口级通常参数规则
     * 4、接口级当前操作参数规则
     *
     * 当规则有冲突时，以后面为准。
     *
     * @return array
     */
    public function getApiRules() {
        $allRules = $this->getRules();

    	$service = DI()->request->get('service', 'Default.Index');
    	list($apiClassName, $action) = explode('.', $service);
        $action = lcfirst($action); 
    	
        $rules = array();
        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }
        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        $apiCommonRules = DI()->config->get('app.apiCommonRules', array());
        if (!empty($apiCommonRules)) {
            $rules = array_merge($apiCommonRules, $rules);
        }

        return $rules;
    }
    
    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载
     */
    public function getRules() {
    	return array();
    }

    /**
     * 过滤器调用
     *
     * 注册的过滤器，请实现PhalApi_Filter::check()接口
     *
     * 可由开发人员根据需要重载
     */
    protected function filterCheck() {
        $filter = DI()->filter;

        if (isset($filter)) {
            $filter->check();
        }
    }
    
    /**
     * 用户身份验证
     *
     * 可由开发人员根据需要重载
     */
    protected function userCheck() {
    	
    }
    
}
