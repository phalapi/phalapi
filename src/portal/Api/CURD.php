<?php
namespace Portal\Api;

use Portal\Common\DataApi as Api;

/**
 * CURD数据接口示例
 */
class CURD extends Api {
    protected function getDataModel() {
        return new \Portal\Model\CURD();
    }
    
    protected function createDataMoreData($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s');
        return $newData;
    }
    
    protected function updateDataRequireKeys() {
        return array('state');
    }
}
