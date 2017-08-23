<?php
namespace App\Api;

class TestTaskDemo extends \PhalApi\Api {

    public function update1() {
        return array('code' => 0);
    }

    public function update2() {
        throw new \PhalApi\Exception\InternalServerErrorException('just for test');
    }
}

