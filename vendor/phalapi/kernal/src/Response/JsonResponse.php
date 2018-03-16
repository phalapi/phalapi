<?php
namespace PhalApi\Response;

use PhalApi\Response;

/**
 * JsonResponse JSON响应类
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

class JsonResponse extends Response {

    /**
     * @var int JSON常量组合的二进制掩码
     * @see http://php.net/manual/en/json.constants.php
     */
    protected $options;

    public function __construct($options = 0) {
        $this->options = $options;

    	$this->addHeaders('Content-Type', 'application/json;charset=utf-8');
    }
    
    protected function formatResult($result) {
        return json_encode($result, $this->options);
    }
    
}
