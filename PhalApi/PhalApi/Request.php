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
 * Request Class
 * 
 * - reponsible for building params by rules and returning error message
 * - need to be use with API rules together
 * 
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */
class PhalApi_Request {

    /**
     * @var array $data 主数据源，接口原始参数
     */
    protected $data = array();

    /**
     * ＠var array $get 备用数据源 $_GET
     */
    protected $get = array();

    /**
     * ＠var array $post 备用数据源 $_POST
     */
    protected $post = array();

    /**
     * ＠var array $request 备用数据源 $_REQUEST
     */
    protected $request = array();

    /**
     * ＠var array $cookie 备用数据源 $_COOKIE
     */
    protected $cookie = array();

    /**
     * @var array $headers 备用数据源 请求头部信息
     */
    protected $headers = array();

    /**
     * @param   array   $data   data source, it can be: $_GET/$_POST/$_REQUEST/etc
     */
    protected $apiName;

    /**
     * @var string 接口服务方法名
     */
    protected $actionName;

    /** 
     * - 如果需要定制已知的数据源（即已有数据成员），则可重载此方法，例
     *
```     
     * class My_Request extend PhalApi_Request{
     *     public function __construct($data = NULL) {
     *         parent::__construct($data);
     *
     *         // handle json
     *         $this->post = json_decode(file_get_contents('php://input'), TRUE);    
     *
     *         // handle xml
     *         $this->post = simplexml_load_string (
     *             file_get_contents('php://input'),
     *             'SimpleXMLElement',
     *             LIBXML_NOCDATA
     *         );
     *         $this->post = json_decode(json_encode($this->post), TRUE);
     *     }  
     * }
```    
     * - 其他格式或其他xml可以自行写函数处理
     *
	 * @param array $data 参数来源，可以为：$_GET/$_POST/$_REQUEST/自定义
     */
    public function __construct($data = NULL) {
        // 主数据源
        $this->data     = $this->genData($data);

        // 备用数据源
        $this->headers  = $this->getAllHeaders();
        $this->get      = $_GET;
        $this->post     = $_POST;
        $this->request  = $_REQUEST;
        $this->cookie   = $_COOKIE;
        
        @list($this->apiName, $this->actionName) = explode('.', $this->getService());
    }

    /**
     * Generate request data
     * 
     * generate different request data according by different project situations, eg:
     * only POST data accepted, or only GET data accepted, or decryped data
     *
     * @param   array   $data   origin data package
     *
     * @return array
     */
    protected function genData($data) {
        if (!isset($data) || !is_array($data)) {
            return $_REQUEST;
        }

        return $data;
    }

    /**
     * Get header infomation
     * 
     * @return array/false
     */
    protected function getAllHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // deal without getallheaders function
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (is_array($value) || substr($name, 0, 5) != 'HTTP_') {
                continue;
            }

            $headerKey = implode('-', array_map('ucwords', explode('_', strtolower(substr($name, 5)))));
            $headers[$headerKey] = $value;
        }

        return $headers;
    }

    /**
     * Get specified header parameter
     *
     * @param   string  $key        header key
     * @param   mixed   $default    default value
     *
     * @return  string
     */
    public function getHeader($key, $default = NULL) {
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    /**
     * Get one API parameter by name
     *
     * @param   string  $key        parameter name
     * @param   mixed   $default    default value
     *
     * @return mixed
     */
    public function get($key, $default = NULL) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    /**
     * Get parameter by rule
     * 
     * build the pramater with rule, and reutrn error message when fail
     *
     * @param   array   $rule       rule, such as: ```array('name' => '', 'type' => '', 'defalt' => ...)```
     *
     * @return mixed
     * @throws PhalApi_Exception_BadRequest
     * @throws PhalApi_Exception_InternalServerError
     */
    public function getByRule($rule) {
        $rs = NULL;

        if (!isset($rule['name'])) {
            throw new PhalApi_Exception_InternalServerError(T('miss name for rule'));
        }

        // 获取接口参数级别的数据集
        $data = !empty($rule['source']) ? $this->getDataBySource($rule['source']) : $this->data;
        $rs = PhalApi_Request_Var::format($rule['name'], $rule, $data);

        if ($rs === NULL && (isset($rule['require']) && $rule['require'])) {
            throw new PhalApi_Exception_BadRequest(T('{name} require, but miss', array('name' => $rule['name'])));
        }

        return $rs;
    }

    /**
     * Get data by source
```     
     * |----------|---------------------|
     * | post     | $_POST              |
     * | get      | $_GET               |
     * | cookie   | $_COOKIE            |
     * | server   | $_SERVER            |
     * | request  | $_REQUEST           |
     * | header   | $_SERVER['HTTP_X']  |
     * |----------|---------------------|
     *   
```     
     * Override this function if you need to get data from other source
     *
     * @throws PhalApi_Exception_InternalServerError
     * @return array 
     */
    protected function &getDataBySource($source) {
        switch (strtoupper($source)) {
        case 'POST' :
            return $this->post;
        case 'GET'  :
            return $this->get;
        case 'COOKIE':
            return $this->cookie;
        case 'HEADER':
            return $this->headers;
        case 'SERVER':
            return $_SERVER;
        case 'REQUEST':
            return $this->request;
        default:
            break;
        }

        throw new PhalApi_Exception_InternalServerError
            (T('unknow source: {source} in rule', array('source' => $source)));
    }

    /**
     * Get all the params
     * @return  array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * Get service name
     *
     * - override this function if you need to specify param name or default service name
     * - should return XXX.XXX at last
     * - should call parent::getService() if can not handle
     *
     * @return  string  service name, e.g. Default.Index
     */
    public function getService() {
        return $this->get('service', 'Default.Index');
    }

    /**
     * Get the class name of serivce
     * @return  string  class name of servcie, like: Api_XXX
     */
    public function getServiceApi() {
        return $this->apiName;
    }

    /**
     * Get the method name of servcie
     * @return  string  method name of service
     */
    public function getServiceAction() {
        return $this->actionName;
    }
}
