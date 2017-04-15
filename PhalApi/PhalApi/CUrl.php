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
 * CUrl Request Class
 *
 * implement simple class to request API with curl
 * 
 * <br>Usage:<br>
 * 
```
 *  // retry 2 times when fail
 *  $curl = new PhalApi_CUrl(2);
 *
 *  // GET
 *  $rs = $curl->get('http://demo.phalapi.net/?service=Default.Index');
 *
 *  // POST
 *  $data = array('username' => 'dogstar');
 *  $rs = $curl->post('http://demo.phalapi.net/?service=Default.Index', $data);
```
 *
 * @package     PhalApi\CUrl
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-01-02
 */

class PhalApi_CUrl {

    /**
     * Retry no more than 10 times.
     */
    const MAX_RETRY_TIMES = 10;

    /**
     * @var     int     $retryTimes     retry times; NOTE, total request times = 1 + retry times
     */
    protected $retryTimes;
    
    protected $header = array();
    
    protected $option = array();
    
    /**
     * Set curl header
     *
     * @param   array   $header     key-value pair like:
```     
     * array(
     *     ['Accept' => 'text/html'],
     *     ['Connection' => 'keep-alive'],
     * )
```     
     *
     * @return $this
     */
    public function setHeader( $header )
    {
        $this->header = array_merge( $this->header, $header);
        return $this;
    }
    
    /**
     * Set curl option
     *
     * - 1、后设置的会覆盖之前的设置
     * - 2、开发者设置的会覆盖框架的设置
     *
     * @param   array   $option     key-value pair
     *
     * @return $this
     */
    public function setOption( $option )
    {
        $this->option = array_merge( $this->option, $option);
        return $this;
    }

    /**
     * @param   int     $retryTimes     retry times, default is 1
     */
    public function __construct($retryTimes = 1) {
        $this->retryTimes = $retryTimes < self::MAX_RETRY_TIMES 
            ? $retryTimes : self::MAX_RETRY_TIMES;
    }

    /**
     * Request by GET
     * 
     * @param   string      $url        the url wait to request
     * @param   int         $timeoutMs  timeout, unit: microsecond
     * @return  string                  response content, return false when time out or fail to connect
     */
    public function get($url, $timeoutMs = 3000) {
        return $this->request($url, array(), $timeoutMs);
    } 

    /**
     * Request by POST
     * 
     * @param   string      $url        the url wait to request
     * @param   array       $data       POST data
     * @param   int         $timeoutMs  timeout, unit: microsecond
     * @return  string                  response content, return false when time out or fail to connect
     */
    public function post($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs);
    }
    
    /**
     *
     * @return array
     */
    protected function getHeaders() {
        $arrHeaders = array();
        foreach ($this->header as $key => $val) {
            $arrHeaders[] = $key . ':' . $val;
        }
        return $arrHeaders;
    }

    /**
     * Request implementation
     * 
     * @param   string      $url        the url wait to request
     * @param   array       $data       POST data
     * @param   int         $timeoutMs  timeout, unit: microsecond
     * @return  string                  response content, return false when time out or fail to connect
     */
    protected function request($url, $data, $timeoutMs = 3000) {
        $options = array(
            CURLOPT_URL                 => $url,
            CURLOPT_RETURNTRANSFER      => TRUE,
            CURLOPT_HEADER              => 0,
            CURLOPT_CONNECTTIMEOUT_MS   => $timeoutMs,
            CURLOPT_HTTPHEADER          => $this->getHeaders(),
        );

        if (!empty($data)) {
            $options[CURLOPT_POST]          = 1;
            $options[CURLOPT_POSTFIELDS]    = $data;
        }
        
        $options = $this->option + $options;
        
        $ch = curl_init();
        curl_setopt_array( $ch, $options);
        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while($rs === FALSE && $curRetryTimes >= 0);

        curl_close($ch);

        return $rs;
    }
}
