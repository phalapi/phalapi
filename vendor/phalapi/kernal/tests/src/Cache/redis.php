<?php

if (!class_exists('Redis')) {

    class Redis {

        public function __call($method, $params) {
            if (empty($_ENV['silence'])) {
                echo 'Redis::' . $method . '() with: ', json_encode($params), " ... \n";
            }
        }

    }
}

