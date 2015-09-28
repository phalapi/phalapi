<?php
/**
 * PhalApi_Translator 国际翻译
 *
 * - 根提供的语言包，进行翻译
 * - 优先使用应用级的翻译，其次是框架默认的
 * 
 * <br>使用示例：<br>
```
 *      //初始化，设置语言
 *      PhalApi_Translator::setLanguage('zh_cn');
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

class PhalApi_Translator {

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
     * 语言设置
     * @param string $language 翻译包的目录名
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
     * 添加更多翻译
     * 
     * - 为扩展类库或者外部提供更方便的方式追加翻译的内容
     *
     * @param string $path 待追加的路径
     * @return NULL
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
     * 取当前的语言
     */
    public static function getLanguage() {
        return self::$language;
    }
}

