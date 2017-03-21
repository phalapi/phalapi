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
 * Translator Class
 *
 * - translate by language package
 * - the priority of translation, project level is higher than system level
 * 
 * <br>Usage:<br>
```
 *      // initialization, set the langugae
 *      PhalApi_Translator::setLanguage('zh_cn');
 *
 *      // translate
 *      $msg = T('hello {name}', array('name' => 'phper'));
 *      var_dump($msg);
```
 *
 * @package     PhalApi\Translator
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

class PhalApi_Translator {

    /**
     * @var     array       $message        translation map
     */
    protected static $message = NULL;

    /**
     * @var     array       $language       language
     */
    protected static $language = 'en';

    /**
     * Translate
     * 
     * @param   string      $key        content to be translated
     * @param   array       $params     dynamic params
     * @return  string
     */
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

    /**
     * Set language
     * 
     * @param   string      $language       language package folder name
     */
    public static function setLanguage($language) {
        self::$language = $language;

        self::$message = array();

        self::addMessage(PHALAPI_ROOT);

        if (defined('API_ROOT')) {
            self::addMessage(API_ROOT);
        }
    }

    /**
     * Add more translation for different language
     * 
     * - provide more translation for libraries or other external lib
     *
     * @param   string      $path       new package folder name to added
     * @return  NULL
     */
    public static function addMessage($path) {
        $moreMessagePath = self::getMessageFilePath($path, self::$language);

        if (file_exists($moreMessagePath)) {
            self::$message = array_merge(self::$message, include $moreMessagePath);
        }
    }

    protected static function getMessageFilePath($root, $language) {
        return implode(DIRECTORY_SEPARATOR, 
            array($root, 'Language', strtolower($language), 'common.php'));
    }

    /**
     * Get current language
     */
    public static function getLanguage() {
        return self::$language;
    }
}

