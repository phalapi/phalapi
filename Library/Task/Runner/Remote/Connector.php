<?php
/**
 * 远程接口连接器 Task_Runner_Remote_Connector
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

abstract class Task_Runner_Remote_Connector {

    protected $host;

    protected $params = array();

    protected $moreParams = array();

    protected $url;
    protected $ret;
    protected $msg;
    protected $data = array();

    public function __construct($config) {
        $this->host = $config['host'];
        $this->moreParams = isset($config['params']) ? $config['params'] : array();
    }

    /**
     * 接口请求，超时时ret为404
     * @param string $service MQ中的接口服务名称，如：Default.Index
     * @param array $params 参数
     * @param int $timeoutMS 接口超时（单位：毫秒）
     * @return array
     */
    public function request($service, $params = array(), $timeoutMs = 3000) {
        $this->url = $this->host . '?service=' . $service;
        $params = array_merge($this->moreParams, $params);

        $apiRs = $this->doRequest($this->url, $params, $timeoutMs);

        if ($apiRs === FALSE) {
            $this->ret = 404;
            $this->msg = T('time out');

            DI()->logger->debug('task request api time out', array('url' => $this->url));

            return $this->getData();
        }

        $rs = json_decode($apiRs, true);

        if (empty($rs) || !isset($rs['ret'])) {
            $this->ret = 500;
            $this->msg = T('nothing return or illegal json: {rs}', array('rs' => $apiRs));
            return $this->getData();
        }

        $this->ret = $rs['ret'];
        $this->data = $rs['data'];
        $this->msg = $rs['msg'];

        return $this->getData();
    }

    public function getRet() {
        return $this->ret;
    }

    public function getData() {
        return $this->data;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getUrl() {
        return $this->url;
    }

    abstract protected function doRequest($url, $data, $timeoutMs);
}
