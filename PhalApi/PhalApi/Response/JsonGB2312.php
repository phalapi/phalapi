<?php
/**
 * PhalApi_Response_JsonGB2312 JSON响应类（GB2312编码专用）
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      Scott <61304770@qq.com> 2015-10-19
 */

class PhalApi_Response_JsonGB2312 extends PhalApi_Response {

    public function __construct() {
    	DI()->logger->info("gb2312 Response enabled.");
    	$this->addHeaders('Content-Type', 'text/html;charset=gb2312');
    }
    
    protected function formatResult($result) {
        //return json_encode($result);
        DI()->logger->info("output JSON.");
        return $this->JSON($result);
    }

	/************************************************************** 
	 * 
	 *    使用特定function对数组中所有元素做处理 
	 *    @param    string    &$array        要处理的字符串 
	 *    @param    string    $function    要执行的函数 
	 *    @return boolean    $apply_to_keys_also        是否也应用到key上 
	 *    @access public 
	 * 
	 *************************************************************/ 
	static protected function arrayRecursive(&$array, $function, $apply_to_keys_also = false) 
	{ 
	    static $recursive_counter = 0; 
	    if (++$recursive_counter > 1000) { 
	        die('possible deep recursion attack'); 
	    } 
	    foreach ($array as $key => $value) { 
	        if (is_array($value)) { 
	            self::arrayRecursive($array[$key], $function, $apply_to_keys_also); 
	        } else { 
	            $array[$key] = $function($value); 
	        } 
	 
	        if ($apply_to_keys_also && is_string($key)) { 
	            $new_key = $function($key); 
	            if ($new_key != $key) { 
	                $array[$new_key] = $array[$key]; 
	                unset($array[$key]); 
	            } 
	        } 
	    } 
	    $recursive_counter--; 
	} 
	 
	/************************************************************** 
	 * 
	 *    将数组转换为JSON字符串（兼容中文） 
	 *    @param    array    $array        要转换的数组 
	 *    @return string        转换得到的json字符串 
	 *    @access public 
	 * 
	 *************************************************************/ 
	protected function JSON($array) { 
	    self::arrayRecursive($array, 'urlencode', true); 
	    $json = json_encode($array); 
	    return urldecode($json); 
	} 
    
}