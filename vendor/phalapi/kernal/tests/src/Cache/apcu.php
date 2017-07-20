<?php

global $__apcu_data;
$__apcu_data = array();

if (!function_exists('apcu_store')) {
    function apcu_store($key, $value, $expire) {
        global $__apcu_data;
        $__apcu_data[$key] = $value;
    }
}

if (!function_exists('apcu_fetch')) {
    function apcu_fetch($key) {
        global $__apcu_data;
        return isset($__apcu_data[$key]) ? $__apcu_data[$key] : NULL;
    }
}

if (!function_exists('apcu_delete')) {
    function apcu_delete($key) {
        global $__apcu_data;
        unset($__apcu_data[$key]);
        return TRUE;
    }
}
