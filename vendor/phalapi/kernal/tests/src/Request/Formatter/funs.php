<?php

function callbackForFormatterTest($value, $rule, $params) {
    //echo "got you!";
    //var_dump($value, $rule, $params);
    return $value . '_fun';
}

