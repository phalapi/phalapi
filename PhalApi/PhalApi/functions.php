<?php
/**
 * 考虑再三，出于人性化关怀，提供要些快速的函数和方法
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-17
 */

/**
 * 获取DI
 * 相当于PhalApi_DI::one()
 * @return PhalApi_DI
 */
function DI() {
    return PhalApi_DI::one();
}

/**
 * 设定语言，SL为setLanguage的简写
 * @param string $language 翻译包的目录名
 */
function SL($language) {
	PhalApi_Translator::setLanguage($language);
}

/**
 * 快速翻译
 * @param string $msg 待翻译的内容
 * @param array $params 动态参数
 */
function T($msg, $params = array()) {
    return PhalApi_Translator::get($msg, $params);
}
