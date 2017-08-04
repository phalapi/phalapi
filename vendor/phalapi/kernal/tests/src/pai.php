<?php

namespace Tests\Api;

class AnotherImpl extends \PhalApi\Api {

    public function doSth() {
        return 'hello wolrd!';
    }

    public function makeSomeTrouble() {
        throw new \Exception('as u can see, i mean to make some trouble');
    }
}

