<?php
/**
 * 考虑再三，出于人性化关怀，提供要些快速的函数和方法
 *
 * @license http://www.phalapi.net/license
 * @link http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2014-12-17
 */

function DI() {
    return PhalApi_DI::one();
}

function T($msg, $params = array()) {
    return PhalApi_Translator::get($msg, $params);
}
