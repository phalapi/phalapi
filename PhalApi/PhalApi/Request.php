<?php
/**
 * PhalApi_Request 参数生成类
 *
 * - 负责根据提供的参数规则，进行参数创建工作，并返回错误信息
 * - 需要与参数规则配合使用
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Request {

	protected $data = array();

    /**
     * @param array $data 参数来源，可以为：$_GET/$_POST/$_REQUEST/自定义
     */
	public function __construct($data = NULL) {
		if (!isset($data) || !is_array($data)) {
            $data = $_REQUEST;
        }
		$this->data = $data;
	}
	
	public function get($key, $default = NULL) {
		return isset($this->data[$key]) ? $this->data[$key] : $default;
	}
	
	/**
     * 获取参数
     *
     * 根据提供的参数规则，进行参数创建工作，并返回错误信息
     *
     * @param $rule array('name' => '', 'type' => '', 'defalt' => ...) 参数规则
     * @return mixed
     */
	public function getByRule($rule) {
		$rs = NULL;
			
        if (!isset($rule['name'])) {
            throw new PhalApi_Exception_InternalServerError(T('miss name for rule'));
        }
        
        $rs = PhalApi_Request_Var::format($rule['name'], $rule, $this->data);
        
        if ($rs === NULL && (isset($rule['require']) && $rule['require'])) {
            throw new PhalApi_Exception_BadRequest(
                T('{name} require, but miss', array('name' => $rule['name']))
            );
        }

        return $rs;
    }
	
	public function getAll() {
		return $this->data;
	}
}
