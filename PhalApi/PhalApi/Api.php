<?php
/**
 * PhalApi_Api 服务基类
 *
 * - 实现身份验证、参数获取生成等操作，并由开发人员自宝义的服务具体类继承
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
     * 1. 根据设置的自定义规则，从$_REQUEST获取所需要的参数，并保存在成员变量内
     * 2. 验证App Key
     * 3. 验证用户身份
     * 
     * @see: PhalApi_Api::createMemberValue()
     */
    public function init() {
    	$this->createMemberValue();
    	
    	$this->filterCheck();
    	
    	$this->checkStatus();
    }
    
    /**
     * 过滤并创建参数
     *
     * 根据客户商调用的方法名字，搜索相应的自定义参数规则进行过滤创建，并把参数存放在类成员变量里面。
     */
    protected function createMemberValue() {
		foreach ($this->getMethodRules() as $key => $rule) {
    		$this->$key = DI()->request->getByRule($rule);
		}
    }

    public function getMethodRules() {
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

    protected function filterCheck() {
        $filter = DI()->filter;

        if (isset($filter)) {
            $filter->check();
        }
    }
    
    /**
     * 验证用户身份
     *
     * 可由开发人员根据需要重载
     */
    protected function checkStatus() {
    	
    }
    
    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载，如果有冲突，以子类为准
     */
    public function getRules() {
    	return array();
    }
    
}
