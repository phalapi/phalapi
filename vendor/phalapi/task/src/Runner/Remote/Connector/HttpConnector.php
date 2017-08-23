<?php
namespace PhalApi\Task\Runner\Remote\Connector;

use PhalApi\Task\Runner\Remote\Connector;

class HttpConnector extends Connector {

    protected function doRequest($url, $data, $timeoutMs) {
        $curl = \PhalApi\DI()->get('curl', 'PhalApi\CUrl');

        return $curl->post($url, $data, $timeoutMs);
    }
}
