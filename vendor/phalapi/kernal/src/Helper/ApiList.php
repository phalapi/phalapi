<?php

namespace PhalApi\Helper;

use PhalApi\Helper\ApiOnline;

defined('D_S') || define('D_S', DIRECTORY_SEPARATOR);

/**
 * ApiList - 在线接口列表文档 - 辅助类
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-11-22
 */
class ApiList extends ApiOnline {

    const API_CATE_TYPE_API_CLASS_NAME = 0;     // 按API类名分类
    const API_CATE_TYPE_API_CLASS_TITLE = 1;    // 按接口模块名称分类

    const API_LIST_SORT_BY_API_NAME = 0;        // 接口列表，根据接口名称排序
    const API_LIST_SORT_BY_API_TITLE = 1;       // 接口列表，根据接口标题排序

    /**
     * @var int $apiCateType 接口分类的方式
     */
    protected $apiCateType;

    /**
     * @var int $apiListSortBy 接口列表的排序方式
     */
    protected $apiListSortBy;

    public function __construct($projectName, $apiCateType = NULL, $apiListSortBy = NULL) {
        $this->projectName = $projectName;

        $this->apiCateType = intval($apiCateType);
        $this->apiListSortBy = intval($apiListSortBy);
    }

    public function render($tplPath = NULL) {
        $tplPath = !empty($tplPath) ? $tplPath : dirname(__FILE__) . '/api_list_tpl.php';
        parent::render($tplPath);

        $composerJson = file_get_contents(API_ROOT . D_S . 'composer.json');
        $composerArr = json_decode($composerJson, TRUE);

        $psr4 = isset($composerArr['autoload']['psr-4']) ? $composerArr['autoload']['psr-4'] : array();
        //检测通用项目API
        $root = !empty($psr4['']) ? $psr4[''] : null;
        if (!empty($root)) {//其它通用项目检测
            unset($psr4['']);
            if (is_string($root)) $root = array($root);
            foreach ($root as $path) {
                foreach (glob(API_ROOT . D_S . $path . D_S . "*", GLOB_ONLYDIR) as $dirName) {
                    $name = pathinfo($dirName, PATHINFO_FILENAME);
                    $psr4[ucfirst($name) . '\\'] = $path . $name;
                }
            }
        }

        // 待排除的方法
        $allPhalApiApiMethods = get_class_methods('\\PhalApi\\Api');

        $allApiS = array();
        $errorMessage = '';

        // 扫描接口文件
        foreach ($psr4 as $namespace => $srcPath) {
            if (!is_string($srcPath) || strpos($srcPath, 'src') === FALSE) {
                continue;
            }

            $allApiS[$namespace] = array();

            $files = listDir(API_ROOT . D_S . $srcPath . D_S . 'Api');
            $filePrefix = rtrim($srcPath, D_S) . D_S . 'Api' . D_S;

            foreach ($files as $aFile) {
                $subValue = strstr($aFile, $filePrefix);
                $apiClassPath = str_replace(array($filePrefix, '.php'), array('', ''), $subValue);
                $apiClassShortName = str_replace(D_S, '_', $apiClassPath);
                $apiClassName = '\\' . $namespace . 'Api\\' . str_replace('_', '\\', $apiClassShortName);

                if (!class_exists($apiClassName)) {
                    continue;
                }

                //  左菜单的标题
                $ref = new \ReflectionClass($apiClassName);
                $title = "//请检测接口服务注释($apiClassName)";
                $desc = '//请使用@desc 注释';
                $isClassIgnore = false; // 是否屏蔽此接口类
                $docComment = $ref->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", $docComment);
                    $comment = trim($docCommentArr[1]);
                    $title = trim(substr($comment, strpos($comment, '*') + 1));
                    foreach ($docCommentArr as $comment) {
                        $pos = stripos($comment, '@desc');
                        if ($pos !== false) {
                            $desc = substr($comment, $pos + 5);
                        }

                        if (stripos($comment, '@ignore') !== false) {
                            $isClassIgnore = true;
                        }
                    }
                }

                if ($isClassIgnore) {
                    continue;
                }

                $apiCateVal = $this->apiCateType == self::API_CATE_TYPE_API_CLASS_TITLE ? $title : $apiClassShortName;
                if (!isset($allApiS[$namespace][$apiCateVal])) {
                    $allApiS[$namespace][$apiCateVal] = array('methods' => array());
                }

                $allApiS[$namespace][$apiCateVal]['title'] = $title;
                $allApiS[$namespace][$apiCateVal]['desc'] = $desc;

                $method = array_diff(get_class_methods($apiClassName), $allPhalApiApiMethods);
                foreach ($method as $mValue) {
                    $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                    if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
                        continue;
                    }

                    $title = '//请检测函数注释';
                    $desc = '//请使用@desc 注释';
                    $methods = '';

                    $isMethodIgnore = false;
                    $docComment = $rMethod->getDocComment();
                    if ($docComment !== false) {
                        $docCommentArr = explode("\n", $docComment);
                        $comment = trim($docCommentArr[1]);
                        $title = trim(substr($comment, strpos($comment, '*') + 1));

                        foreach ($docCommentArr as $comment) {
                            $pos = stripos($comment, '@desc');
                            if ($pos !== false) {
                                $desc = substr($comment, $pos + 5);
                            }

                            if (stripos($comment, '@ignore') !== false) {
                                $isMethodIgnore = true;
                            }

                            //@method注释
                            $pos = stripos($comment, '@method');
                            if ($pos !== FALSE) {
                                $methods = substr($comment, $pos + 8);
                                continue;
                            }
                        }
                    }

                    if ($isMethodIgnore) {
                        continue;
                    }

                    $service = trim($namespace, '\\') . '.' . $apiClassShortName . '.' . ucfirst($mValue);
                    $allApiS[$namespace][$apiCateVal]['methods'][$service] = array(
                        'service' => $service,
                        'title' => $title,
                        'desc' => $desc,
                        'methods' => $methods,
                    );
                }
            }
        }
        // 主题风格，fold = 折叠，expand = 展开
        $theme = isset($_GET['type']) ? $_GET['type'] : 'fold';
        if (!in_array($theme, array('fold', 'expand'))) {
            $theme = 'fold';
        }
        // 搜索时，强制采用展开主题
        if (!empty($_GET['keyword'])) {
            $theme = 'expand';
        }

        // 字典排列与过滤
        foreach ($allApiS as $namespace => &$subAllApiS) {
            if (empty($subAllApiS)) {
                unset($allApiS[$namespace]);
                continue;
            }

            // 接口大列表排序
            if ($this->apiListSortBy == self::API_LIST_SORT_BY_API_TITLE) {
                // 根据自定义接口标题排序
                $sortTiles = array_column($subAllApiS, 'title');
                // @link https://www.php.net/manual/zh/function.array-multisort.php 示例 #4
                array_multisort($sortTiles, SORT_ASC, SORT_STRING, $subAllApiS);
            } else {
                // 默认根据接口名称排序
                ksort($subAllApiS);
            }

            // 接口小列表排序
            foreach ($subAllApiS as &$subMethods) {
                if ($this->apiListSortBy == self::API_LIST_SORT_BY_API_TITLE) {
                    $subSortTitles = array_column($subMethods['methods'], 'title');
                    array_multisort($subSortTitles, SORT_ASC, SORT_STRING, $subMethods['methods']);
                } else {
                    ksort($subMethods);
                }
            }
        }
        unset($subAllApiS);

        $projectName = $this->projectName;

        include $tplPath;
    }

    public function makeApiServiceLink($service, $theme = '') {
        $concator = strpos($this->getUri(), '?') ? '&' : '?';
        return $this->getUri() . $concator . 'service=' . $service . '&detail=1' . '&type=' . $theme;
    }

    public function getUri() {
        return $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
    }

    public function makeThemeButton($theme) {
        $curUrl = $_SERVER['SCRIPT_NAME'];
        if ($theme == 'fold') {
            echo '<div style="float: right"><a href="' . $curUrl . '?type=expand">'.\PhalApi\T('Expand All').'</a></div>';
        } else {
            echo '<div style="float: right"><a href="' . $curUrl . '?type=fold">'.\PhalApi\T('Fold All').'</a></div>';
        }
    }

}

function listDir($dir) {
    $dir .= substr($dir, -1) == D_S ? '' : D_S;
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}

function saveHtml($webRoot, $name, $string) {
    $dir = $webRoot . D_S . 'docs';
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $handle = fopen($dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite($handle, $string);
    fclose($handle);
}

