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
        return $this->request($url, FALSE, $timeoutMs);
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
     * Request implementation
     * 
     * @param   string      $url        the url wait to request
     * @param   array       $data       POST data
     * @param   int         $timeoutMs  timeout, unit: microsecond
     * @return  string                  response content, return false when time out or fail to connect
     */
    protected function request($url, $data, $timeoutMs = 3000) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while($rs === FALSE && $curRetryTimes >= 0);

        curl_close($ch);

        return $rs;
    }
}
