<?php
namespace PhalApi;

/**
 * Translator 国际翻译
 *
 * - 根提供的语言包，进行翻译
 * - 优先使用应用级的翻译，其次是框架默认的
 * 
 * <br>使用示例：<br>
```
 *      //初始化，设置语言
 *      Translator::setLanguage('zh_cn');
 *
 *      //翻译
 *      $msg = T('hello {name}', array('name' => 'phper'));
 *      var_dump($msg);
```
 *
 * @package     PhalApi\Translator
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

class Translator {

	/**
	 * @var array $message 翻译的映射
	 */
    protected static $message = NULL;

	/**
	 * @var array $language 语言
	 */
	protected static $language = 'en';

    /**
     * 获取翻译
     * @param string $key 翻译的内容
     * @param array $params 动态参数
     * @return string
     */
    public static function get($key, $params = array()) {
        if (static::$message === NULL) {
            static::setLanguage('en');
        }

        $rs = isset(static::$message[$key]) ? static::$message[$key] : $key;

        $names = array_keys($params);
        $names = array_map(array('\\PhalApi\\Translator', 'formatVar'), $names);

        return str_replace($names, array_values($params), $rs);
    }

    public static function formatVar($name) {
        return '{' . $name . '}';
    }

    /**
     * 语言设置
     * @param string $language 翻译包的目录名
     */
    public static function setLanguage($language) {
        static::$language = $language;

        static::$message = array();

        $kernalLanguageFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..';
        static::addMessage($kernalLanguageFolder);

        if (defined('API_ROOT')) {
            static::addMessage(API_ROOT);
        }
    }

    /**
     * 添加更多翻译
     * 
     * - 为扩展类库或者外部提供更方便的方式追加翻译的内容
     *
     * @param string $path 待追加的路径
     * @return NULL
     */
    public static function addMessage($path) {
        $moreMessagePath = static::getMessageFilePath($path, static::$language);

        if (file_exists($moreMessagePath)) {
            static::$message = array_merge(static::$message, include $moreMessagePath);
        }
    }

    protected static function getMessageFilePath($root, $language) {
        return implode(DIRECTORY_SEPARATOR, 
            array($root, 'language', strtolower($language), 'common.php'));
    }

    /**
     * 取当前的语言
     */
    public static function getLanguage() {
        return static::$language;
    }
}

