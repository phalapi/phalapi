<?php
/**
 * JSON响应类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

class PhalApi_Response_JsonP extends PhalApi_Response {

    protected $callback = '';

    public function __construct($callback) {
        $this->callback = $this->clearRss($callback);

        $this->addHeaders('Content-Type', 'text/javascript; charset=utf-8');
    }

    //TODO
    protected function clearRss($callback) {
        return $callback;
    }

    protected function formatResult($result) {
        echo $this->callback . '(' . json_encode($result) . ')';
    }
}
