<?php

class Domain_Examples_CURD {

    public function insert($newData) {
        $newData['post_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new Model_Examples_CURD();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new Model_Examples_CURD();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new Model_Examples_CURD();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new Model_Examples_CURD();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new Model_Examples_CURD();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
}
