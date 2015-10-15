<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PhalApiClientParser.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PhalApiClientParserJson.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PhalApiClientFilter.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PhalApiClientResponse.php';

class PhalApiClient {

    protected $host;
    //protected PhalApiClientFilter $filter;
    //protected PhalApiClientParser $parser;
    protected $filter;
    protected $parser;
    protected $service;
    protected $timeoutMs;
    protected $params = array();

    public static function create() {
        return new self();
    }

    protected function __construct() {
        $this->parser = new PhalApiClientParserJson();
    }

    public function withHost($host) {
        $this->host = $host;
        return $this;
    }

    public function withFilter(PhalApiClientFilter $filter) {
        $this->filter = $filter;
        return $this;
    }

    public function withParser(PhalApiClientParser $parser) {
        $this->parser = $parser;
        return $this;
    }

    public function withService($service) {
        $this->service = $service;
        return $this;
    }

    public function withParams($name, $value) {
        $this->params[$name] = $value;
        return $this;
    }

    public function withTimeout($timeoutMS) {
        $this->timeoutMS = $timeoutMS;
        return $this;
    }

    public function request() {
        $url = $this->host;

        if (!empty($this->service)) {
            $url .= '?service=' . $this->service;
        }
        if ($this->filter !== null) {
            $this->filter->filter($this->service, $this->params);
        }

        $rs = $this->doRequest($url, $this->params, $this->timeoutMs);

        return $this->parser->parse($rs);
    }

    protected function doRequest($url, $data, $timeoutMs = 3000)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }
}
