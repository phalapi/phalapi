<?php

/**
 * PhalApi 应用类
 *
 * - 实现远程服务的响应、调用等操作
 *
 * @author: dogstar 2014-12-17
 */
 
defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__));

defined('PHALAPI_VERSION') || define('PHALAPI_VERSION', '1.1.0');

require_once PHALAPI_ROOT . DIRECTORY_SEPARATOR . 'PhalApi' . DIRECTORY_SEPARATOR . 'Loader.php';

class PhalApi
{
    
    /**
     * 响应操作
     * 通过工厂方法创建合适的控制器，然后调用指定的方法，最后返回格式化的数据。
     * @return mixed 根据配置的或者手动设置的返回格式，将结果返回，其结果包含以下元素：
     *  array(
     *      'res' => 0,	        //服务器响应状态
     *      'data' => null,	    //正常并成功响应后，返回给客户端的数据	
     *	    'msg' => '',		//错误提示信息
     *  );
     */
    public function response()
    {
    	$rs = new PhalApi_Response();
    	
    	$rs->addHeaders('Content-Type', 'text/html;charset=utf-8');
    	
    	try{
    		$controller = PhalApi_ApiFactory::generateService(); 
    		
    		$service = DI()->request->get('service', 'Default.Index');
    		list($apiClassName, $action) = explode('.', $service);
				
        	$rs->setData(call_user_func(array($controller, $action)));
    	} catch (PhalApi_Exception $ex){
    		$rs->setRet($ex->getCode());
        	$rs->setMsg($ex->getMessage());
    	} catch (Exception $ex){
    		throw $ex;
    	}
		
    	return $rs;
    }
    
}
