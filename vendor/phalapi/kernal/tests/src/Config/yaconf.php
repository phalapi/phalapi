<?php

if (!class_exists('Yaconf', false)) {
    class Yaconf {
        public static function __callStatic($method, $params) {
            if (empty($_ENV['silence'])) {
                echo "Yaconf::$method()...\n";
            }

            if ($method == 'get') {
                return 'PhalApi';
            } else if ($method == 'has') {
                return true;
            }
        }
    }
}

