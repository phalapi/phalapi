<?php

namespace Tests\Api {

    class InnerRunner extends \PhalApi\Api {
        public function go() {
            return array('home');
        }
    }
}
