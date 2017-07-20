<?php
namespace App\Domain\Examples;

use App\Model\Examples\CURD as ModelCURD;

class CURD {

    public function insert($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelCURD();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelCURD();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelCURD();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelCURD();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelCURD();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
}
