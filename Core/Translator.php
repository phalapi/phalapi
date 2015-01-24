<?php

class Core_Translator
{
    protected static $message = null;

    public static function get($key, $params = array())
    {
        if(self::$message === null) {
            self::setLanguage('en');
        }

        $rs = isset(self::$message[$key]) ? self::$message[$key] : $key;

        $names = array_keys($params);
        $names = array_map(array('Core_Translator', 'formatVar'), $names);

        return str_replace($names, array_values($params), $rs);
    }

    public static function setLanguage($language)
    {
        $path = dirname(__FILE__) . '/../Language/' . strtolower($language) . '/common.php';

        if (!file_exists($path)) {
            throw new Core_Exception_RuntimeError($language . ' not found', 18);
        }

        self::$message = include $path;
    }

    public static function formatVar($name)
    {
        return '{' . $name . '}';
    }
}
