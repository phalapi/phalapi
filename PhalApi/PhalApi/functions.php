<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * Consider again and again, PhalApi provide some short functions for fast development
 *
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-17
 */

/**
 * Get DI singleton
 * 
 * It is equals to PhalApi_DI::one()
 * 
 * @return PhalApi_DI
 */
function DI() {
    return PhalApi_DI::one();
}

/**
 * Setting language 
 * 
 * SL is short for setLanguage
 * 
 * @param   string  $language   the folder name of translation package
 */
function SL($language) {
    PhalApi_Translator::setLanguage($language);
}

/**
 * Fast Translation
 * 
 * It is equals to PhalApi_Translator::get($msg, $params)
 * 
 * @param   string  $msg        the content to be translated
 * @param   array   $params     dynamic params
 */
function T($msg, $params = array()) {
    return PhalApi_Translator::get($msg, $params);
}
