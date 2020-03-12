<?php
namespace Portal\Api\PhalApi\MiniTea;

use Portal\Common\DataApi;

/**
 * 茶店微信小程序
 * @ ignore
 * @author dogstar 20200308
 */
class TeaOrder extends DataApi {
    
    protected function getDataModel() {
        return new \Portal\Model\PhalApi\MiniTea\Order();
    }

}
