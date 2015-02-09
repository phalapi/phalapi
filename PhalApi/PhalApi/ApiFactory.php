<?php
/**
 * PhalApi_ApiFactory 创建控制器类 工厂方法
 *
 * - 将创建与使用分离，简化客户调用，负责控制器复杂的创建过程
 *
 *      //根据请求生成对应的接口服务，并进行初始化
 *      $api = PhalApi_ApiFactory::generateService();
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_ApiFactory {
	/**
     * 创建服务器
     * 根据客户端提供控制器名称和需要调用的方法进行创建工作，如果创建失败，则抛出相应的自定义异常
     *
     * 创建过程主要如下：
     * 1. 是否缺少控制器名称和需要调用的方法
     * 2. 控制器文件是否存在，并且控制器是否存在
     * 3. 方法是否可调用
     * 4. 控制器是否初始化成功
     *
     * @return PhalApi_Api 自定义的控制器
     */
	static function generateService($isInitialize = true) {
		$service = DI()->request->get('service', 'Default.Index');
		
		$serviceArr = explode('.', $service);

		if (count($serviceArr) < 2) {
            throw new PhalApi_Exception_BadRequest(
                T('service ({service}) illegal', array('service' => $service))
            );
        }

		list($className, $action) = $serviceArr;
	    $className = 'Api_' . ucfirst($className);
        $action = lcfirst($action);

        if(!class_exists($className)) {
            throw new PhalApi_Exception_BadRequest(
                T('no such service as {className}', array('className' => $service))
            );
        }
        		
    	$controller = new $className();
    			
    	if(!method_exists($controller, $action) || !is_callable(array($controller, $action))) {
            throw new PhalApi_Exception_BadRequest(
                T('no such service as {className}', array('className' => $service))
            );
    	}

        if ($isInitialize) {
            $controller->init();
        }
		
		return $controller;
	}
	
}
