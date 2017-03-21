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
 * Response Class
 *
 * - all of the reponse status, and format the result
 * - 200 stands for success, while 400 stands for illegal request, and 500 stands for server internal error
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

abstract class PhalApi_Response {

    /**
     * @var     int     $ret    reponse return code, 200 stands for success, while 400 stands for illegal request, and 500 stands for server internal error
     */
    protected $ret = 200;
    
    /**
     * @var     array   $data   result wait to be send to client
     */
    protected $data = array();
    
    /**
     * @var     string  $msg    error message to return 
     */
    protected $msg = '';
    
    /**
     * @var     array   $headers    reponse headers
     */
    protected $headers = array();

    /** ------------------ setter ------------------ **/

    /**
     * Set the response status
     * 
     * @param   int     $ret    response status, such as: 2xx, 4xx, 5xx
     * @return  PhalApi_Response
     */
    public function setRet($ret) {
        $this->ret = $ret;
        return $this;
    }
    
    /**
     * Set the response data
     * 
     * @param   array/string    $data   result data to client, we suggest return array data, not string data
     * @return  PhalApi_Response
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set the error message
     * @param   string          $msg    error message
     * @return  PhalApi_Response
     */
    public function setMsg($msg) {
        $this->msg = $msg;
        return $this;
    }
    
    /**
     * Add header
     * 
     * @param   string  $key        header name
     * @param   string  $content    header content
     */
    public function addHeaders($key, $content) {
        $this->headers[$key] = $content;
    }

    /** ------------------ output result ------------------ **/

    /**
     * Ouput
     */
    public function output() {
        $this->handleHeaders($this->headers);

        $rs = $this->getResult();

        echo $this->formatResult($rs);
    }
    
    /** ------------------ getter ------------------ **/
    
    public function getResult() {
        $rs = array(
            'ret' => $this->ret,
            'data' => $this->data,
            'msg' => $this->msg,
        );

        return $rs;
    }

    /**
     * Get Headers
     * 
     * @param   string          $key    header name
     * @return  string/array    return NULL when header not exists, and return all headers when ```$key``` is NULL
     */
    public function getHeaders($key = NULL) {
        if ($key === NULL) {
            return $this->headers;
        }

        return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
    }

    /** ------------------ internal functions ------------------ **/

    protected function handleHeaders($headers) {
        foreach ($headers as $key => $content) {
            @header($key . ': ' . $content);
        }
    }

    /**
     * Format result data
     *
     * @param   array    $result    reponse data to client
     *
     * @see PhalApi_Response::getResult()
     */
    abstract protected function formatResult($result);
}
