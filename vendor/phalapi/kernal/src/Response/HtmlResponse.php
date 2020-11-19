<?php
namespace PhalApi\Response;

/**
 * HtmlResponse 响应类
 *
 * - 支持页面渲染返回输出
 *
 * \PhalApi\DI()->response = new \PhalApi\Response\HtmlResponse(); // 重新注册
 * 
 * @author 大卫 dogstar
 */
class HtmlResponse extends JsonResponse
{
    protected $namespace;   // 命名空间
    protected $themes;  // 模板主题
    protected $name = 'Site/index'; // 要调用的模板名
    protected $param = array();     // 模板参数[app数据、公共数据等]
    protected $ext;


    public function __construct($namespace = 'app', $themes = 'Default', $ext = '.php') {
        $this->namespace = $namespace;
        $this->themes = $themes;
        $this->ext = $ext;

        $this->addHeaders('Content-Type', 'text/html;charset=utf-8');
    }

    /**
     * 格式化需要输出返回的结果
     * @param $result
     * @return false|string
     * @throws \Exception
     */
    protected function formatResult($result)
    {
        $this->adjustHttpStatus();
        $this->namespace = \PhalApi\DI()->request->getNamespace();
        $api        = \PhalApi\DI()->request->getServiceApi();
        $action     = \PhalApi\DI()->request->getServiceAction();
        $this->name = $api . '/' . $action;
        if ($this->ret === 200) {
            return $this->load($this->name, $result['data']);
        } elseif ($this->ret > 200 && $this->ret <=206) {
            return $this->load($api, $result['data']);
        }

        return $this->load('error', $result);
    }

    /**
     * 根据状态码调整Http响应状态码
     */
    public function adjustHttpStatus()
    {
        $httpStatus = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
        );

        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        $str = isset($httpStatus[$this->ret]) ? $protocol .' '. $this->ret .' '. $httpStatus[$this->ret] : "HTTP/1.1 {$this->ret} Unknown Http Status Code";
        @header($str);

        return $this;
    }

    /**
     * 注入单个变量
     * @param $k
     * @param $v
     */
    public function fetch($k, $v)
    {
        $this->param[$k] = $v;
    }

    /**
     * 注入数组变量
     * @param array $param 参数 $K => $v
     */
    public function assign($param = array())
    {
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                $this->fetch($k, $v);
            }
        }
    }

    /**
     * 设置模板主题
     * @param $themes
     */
    public function setThemes($themes)
    {
        $this->themes = $themes;
    }

    /**
     * 获取模板路径
     * @param string $name
     * @return string
     */
    private function path($name = '')
    {
        return API_ROOT . '/src/' . strtolower($this->namespace) . '/View/' . $this->themes . '/' . $name . $this->ext;
    }

    /**
     * 装载模板
     * @param string $name html文件名称
     * @param array $param
     * @return false|string
     * @throws \Exception
     */
    public function load($name, $param = array())
    {
        $viewTplPath = $this->path($name);
        if (!file_exists($viewTplPath)) {
            exit($viewTplPath . ' 模板文件不存在');
        }
        // 合并参数
        $param = is_array($param) ? array_merge($this->param, $param) : $this->param;
        // 将数组键名作为变量名，如果有冲突，则覆盖已有的变量
        extract($param, EXTR_OVERWRITE);
        unset($param);

        ob_start();
        //ob_implicit_flush(false);
        require($viewTplPath);
        // 获取当前缓冲区内容
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
