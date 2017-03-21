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

    protected $data = array();

    protected $headers = array();

    /**
     * @param   array   $data   data source, it can be: $_GET/$_POST/$_REQUEST/etc
     */
    public function __construct($data = NULL) {
        $this->data    = $this->genData($data);
        $this->headers = $this->getAllHeaders();
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
     * @return Ambigous <unknown, multitype:>
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
     * @return  mixed
     */
    public function getByRule($rule) {
        $rs = NULL;

        if (!isset($rule['name'])) {
            throw new PhalApi_Exception_InternalServerError(T('miss name for rule'));
        }

        $rs = PhalApi_Request_Var::format($rule['name'], $rule, $this->data);

        if ($rs === NULL && (isset($rule['require']) && $rule['require'])) {
            throw new PhalApi_Exception_BadRequest(T('{name} require, but miss', array('name' => $rule['name'])));
        }

        return $rs;
    }

    /**
     * Get all the params
     * 
     * @return array
     */
    public function getAll() {
        return $this->data;
    }
}
