<?php
/**
 * 本地调度器 Task_Runner_Local
 * 
 * - 本地内部调度
 * - 不能在Api请求时进行此调度
 * 
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

class Task_Runner_Local extends Task_Runner {

    protected function youGo($service, $params) {
        $params['service'] = $service;

        DI()->request = new PhalApi_Request($params);
        DI()->response = new PhalApi_Response_Json();

        $phalapi = new PhalApi();
        $rs = $phalapi->response();
        $apiRs = $rs->getResult();

        if ($apiRs['ret'] != 200) {
            DI()->logger->debug('task local go fail', 
                array('servcie' => $service, 'params' => $params, 'rs' => $apiRs));

            return FALSE;
        }

        return TRUE;
    }

}

