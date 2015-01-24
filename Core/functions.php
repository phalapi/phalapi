<?php
/**
 * 考虑再三，出于人性化关怀，提供要些快速的函数和方法
 *
 * @author: dogstar 2014-12-17
 */

function DI()
{
    return Core_DI::one();
}

function T($msg, $params = array())
{
    return Core_Translator::get($msg, $params);
}
