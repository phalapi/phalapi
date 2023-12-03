<?php
namespace App\Common;

class Tracer extends \PhalApi\Helper\Tracer {

    public function sql($statement) {
        $statement = 'demo-api.phalapi.net|' . $statement;
        return parent::sql($statement);
    }
}
