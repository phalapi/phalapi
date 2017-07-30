<?php

/**
 * PhalApi_Request 参数生成类
 * - 负责根据提供的参数规则，进行参数创建工作，并返回错误信息
 * - 需要与参数规则配合使用
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */
class PhalApi_Request {

    /**
     * @var array $data 主数据源，接口原始参数
     */
    protected $data = array();

    /**
     * @var array $get 备用数据源 $_GET
     */
    protected $get = array();

    /**
     * @var array $post 备用数据源 $_POST
     */
    protected $post = array();

    /**
     * @var array $request 备用数据源 $_REQUEST
     */
    protected $request = array();

    /**
     * @var array $cookie 备用数据源 $_COOKIE
     */
    protected $cookie = array();

    /**
     * @var array $headers 备用数据源 请求头部信息
     */
    protected $headers;

    /**
     * @var string 接口服务类名
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
     *         // json处理
     *         $this->post = json_decode(file_get_contents('php://input'), TRUE);    
     *
     *         // 普通xml处理
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
        $this->get      = $_GET;
        $this->post     = $_POST;
        $this->request  = $_REQUEST;
        $this->cookie   = $_COOKIE;
        
        @list($this->apiName, $this->actionName) = explode('.', $this->getService());
    }

    /**
     * 生成请求参数
     *
     * - 此生成过程便于项目根据不同的需要进行定制化参数的限制，如：如只允许接受POST数据，或者只接受GET方式的service参数，以及对称加密后的数据包等
     * - 如果需要定制默认数据源，则可以重载此方法
	 *
     * @param array $data 接口参数包
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
     * 初始化请求Header头信息
     * @return array|false
     */
    protected function getAllHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        //对没有getallheaders函数做处理
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
     * 获取请求Header参数
     *
     * @param string $key     Header-key值
     * @param mixed  $default 默认值
     *
     * @return string
     */
    public function getHeader($key, $default = NULL) {
        // 延时加载，提升性能
        if ($this->headers === NULL) {
            $this->headers = $this->getAllHeaders();
        }

        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    /**
     * 直接获取接口参数
     *
     * @param string $key     接口参数名字
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    public function get($key, $default = NULL) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    /**
     * 根据规则获取参数
     * 根据提供的参数规则，进行参数创建工作，并返回错误信息
     *
     * @param $rule array('name' => '', 'type' => '', 'defalt' => ...) 参数规则
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
        $data = !empty($rule['source']) && substr(php_sapi_name(), 0, 3) != 'cli' 
            ? $this->getDataBySource($rule['source']) 
            : $this->data;

        $rs = PhalApi_Request_Var::format($rule['name'], $rule, $data);

        if ($rs === NULL && (isset($rule['require']) && $rule['require'])) {
            throw new PhalApi_Exception_BadRequest(T('{name} require, but miss', array('name' => $rule['name'])));
        }

        return $rs;
    }

    /**
     * 根据来源标识获取数据集
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
     * - 当需要添加扩展其他新的数据源时，可重载此方法
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
            if ($this->headers === NULL) {
                $this->headers = $this->getAllHeaders();
            }
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
     * 获取全部接口参数
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * 获取接口服务名称
     *
     * - 子类可重载此方法指定参数名称，以及默认接口服务
     * - 需要转换为原始的接口服务格式，即：XXX.XXX
     * - 为保持兼容性，子类需兼容父类的实现
     * - 参数名为：service，支持短参数名：s，并优先完全参数名
     *
     * @return string 接口服务名称，如：Default.Index
     */
    public function getService() {
        return $this->get('service', $this->get('s', 'Default.Index'));
    }

    /**
     * 获取接口服务名称中的接口类名
     * @return string 接口类名
     */
    public function getServiceApi() {
        return $this->apiName;
    }

    /**
     * 获取接口服务名称中的接口方法名
     * @return string 接口方法名
     */
    public function getServiceAction() {
        return $this->actionName;
    }
}
