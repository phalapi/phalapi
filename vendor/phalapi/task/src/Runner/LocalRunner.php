<?php
namespace PhalApi\Task\Runner;

use PhalApi\Task\Runner;
use PhalApi\Request;
use PhalApi\Response\JsonResponse;
use PhalApi\PhalApi;

/**
 * 本地调度器 LocalRunner
 * 
 * - 本地内部调度
 * - 不能在Api请求时进行此调度
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class LocalRunner extends Runner {

    protected function youGo($service, $params) {
        $params['service'] = $service;

        \PhalApi\DI()->request = new Request($params);
        \PhalApi\DI()->response = new JsonResponse();

        $phalapi = new PhalApi();
        $rs = $phalapi->response();
        $apiRs = $rs->getResult();

        if ($apiRs['ret'] != 200) {
            \PhalApi\DI()->logger->debug('task local go fail', 
                array('servcie' => $service, 'params' => $params, 'rs' => $apiRs));

            return FALSE;
        }

        return TRUE;
    }

}

