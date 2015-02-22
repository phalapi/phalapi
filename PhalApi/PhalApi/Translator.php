<?php
/**
 * PhalApi_Translator 国际翻译
 *
 * - 根提供的语言包，进行翻译
 * - 优先使用应用级的翻译，其次是框架默认的
 * 
 * 使用示例
 *      //初始化，设置语言
 *      PhalApi_Translator::setLanguage('zh_cn');
 *
 *      //翻译
 *      $msg = T('hello {name}', array('name' => 'phper'));
 *      var_dump($msg);
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

class PhalApi_Translator {

    protected static $message = NULL;

    public static function get($key, $params = array()) {
        if (self::$message === NULL) {
            self::setLanguage('en');
        }

        $rs = isset(self::$message[$key]) ? self::$message[$key] : $key;

        $names = array_keys($params);
        $names = array_map(array('PhalApi_Translator', 'formatVar'), $names);

        return str_replace($names, array_values($params), $rs);
    }

    public static function formatVar($name) {
        return '{' . $name . '}';
    }

    public static function setLanguage($language) {
        self::$message = array();

        $messagePath = self::getMessageFilePath(PHALAPI_ROOT, $language);

        if (file_exists($messagePath)) {
            self::$message = include $messagePath;
        }

        if (defined('API_ROOT')) {
            $appMessagePath = self::getMessageFilePath(API_ROOT, $language);

            if (file_exists($appMessagePath)) {
                self::$message = array_merge(self::$message, include $appMessagePath);
            }
        }
    }

    protected static function getMessageFilePath($root, $language) {
        return implode(DIRECTORY_SEPARATOR, 
            array($root, 'Language', strtolower($language), 'common.php'));
    }
}
