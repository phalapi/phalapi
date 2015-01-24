<?php

/**
 * PhalApi 应用类
 *
 * - 实现远程服务的响应、调用等操作
 *
 * @author: dogstar 2014-12-17
 */
 
defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__));

defined('PHALAPI_VERION') || define('PHALAPI_VERION', '1.0.0');

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
    	$di = Core_DI::one();
    	
    	$rs = new Core_Response();
    	
    	$rs->addHeaders('Content-Type', 'text/html;charset=utf-8');
    	
    	try{
    		$controller = Core_ApiFactory::generateService(); 
    		
    		$service = $di->request->get('service', 'Default.index');
    		list($apiClassName, $action) = explode('.', $service);
				
        	$rs->setData(call_user_func(array($controller, $action)));
    	} catch (Core_Exception $ex){
    		$rs->setRet($ex->getCode());
        	$rs->setMsg($ex->getMessage());
    	} catch (Exception $ex){
    		throw $ex;
    	}
		
    	return $rs;
    }
    
}
