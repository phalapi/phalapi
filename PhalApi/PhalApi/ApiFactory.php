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
 * Api Factory Class
 * 
 * - factory method to craete controller objects
 * - separate creation and usage, simplify client development
 * - only be responsible for complicate creation
 *
 * <br>Usage:</br>
```
 *      // create and init service by request(?service=XXX.XXX)
 *      $api = PhalApi_ApiFactory::generateService();
```
 * @package     PhalApi\Api
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_ApiFactory {

    /**
     * Generate service
     * according the service name and method name request from client; throw related custom exception when fail
     *
     * The main process is as follows:
     * - 1. check whether miss controller name or method name
     * - 2. make sure that both controller file and controller class exists
     * - 3. whether method is callable or not
     * - 4. whether succeed to intitailze controller
     *
     * @param   boolen          $isInitialize           whether try to initialize after creation
     * @param   string          $_REQUEST['service']    service name, format: XXX.XXX
     * @return  PhalApi_Api     Api implements
     *
     * @uses    PhalApi_Api::init()
     * @throws  PhalApi_Exception_BadRequest 非法请求下返回400
     */
    static function generateService($isInitialize = TRUE) {
        $service = DI()->request->get('service', 'Default.Index');
        
        $serviceArr = explode('.', $service);

        if (count($serviceArr) < 2) {
            throw new PhalApi_Exception_BadRequest(
                T('service ({service}) illegal', array('service' => $service))
            );
        }

        list ($apiClassName, $action) = $serviceArr;
        $apiClassName = 'Api_' . ucfirst($apiClassName);
        // $action = lcfirst($action);

        if (!class_exists($apiClassName)) {
            throw new PhalApi_Exception_BadRequest(
                T('no such service as {service}', array('service' => $service))
            );
        }
                
        $api = new $apiClassName();

        if (!is_subclass_of($api, 'PhalApi_Api')) {
            throw new PhalApi_Exception_InternalServerError(
                T('{class} should be subclass of PhalApi_Api', array('class' => $apiClassName))
            );
        }
                
        if (!method_exists($api, $action) || !is_callable(array($api, $action))) {
            throw new PhalApi_Exception_BadRequest(
                T('no such service as {service}', array('service' => $service))
            );
        }

        if ($isInitialize) {
            $api->init();
        }
        
        return $api;
    }
    
}
