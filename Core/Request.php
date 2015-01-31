<?php
/**
 * Core_Request 参数生成类
 *
 * - 负责根据提供的参数规则，进行参数创建工作，并返回错误信息
 * - 需要与参数规则配合使用
 *
 * @author: dogstar 2014-10-02
 */

class Core_Request
{
	protected $data = array();
	
	public function __construct($data = null)
	{
		if (!isset($data) || empty($data)) {
            $data = $_REQUEST;
        }
		$this->data = $data;
	}
	
	public function get($key, $default = null)
	{
		return isset($this->data[$key]) ? $this->data[$key] : $default;
	}
	
	/**
     * 获取参数
     * 根据提供的参数规则，进行参数创建工作，并返回错误信息
     * @param $rule array('name' => '', 'type' => '', 'defalt' => ...) 参数规则
     */
	public function getByRule($rule)
	{
		$rs = null;
			
        if (!isset($rule['name'])) {
            throw new Core_Exception_RuntimeError(T('miss name for rule'));
        }
        
        $rs = Core_Request_Var::format($rule['name'], $rule, $this->data);
        
        if ($rs === null && (isset($rule['require']) && $rule['require'])) {
        	throw new Core_Exception_IllegalParam(T('wrong param: {name}', array('name' => $rule['name'])));
        }

        return $rs;
    }
	
	public function getAll()
	{
		return $this->data;
	}
}
