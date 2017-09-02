<?php
namespace PhalApi\Helper;

use PhalApi\Helper\ApiOnline;

defined('D_S') || define('D_S', DIRECTORY_SEPARATOR);

class ApiList extends ApiOnline {

    public function render() {
        $composerJson = file_get_contents(API_ROOT . D_S . 'composer.json');
        $composerArr = json_decode($composerJson, TRUE);

        $psr4 = isset($composerArr['autoload']['psr-4']) ? $composerArr['autoload']['psr-4'] : array();

        // 待排除的方法
        $allPhalApiApiMethods = get_class_methods('\\PhalApi\\Api');

        $allApiS = array();
        $errorMessage = '';

        // 扫描接口文件
        foreach ($psr4 as $namespace => $srcPath) {
            if (!is_string($srcPath) || strpos($srcPath, 'src') === FALSE) {
                continue;
            }

            $files = listDir(API_ROOT . D_S . $srcPath . D_S . 'Api');
            $filePrefix = rtrim($srcPath, D_S) . D_S .'Api' . D_S;

            foreach ($files as $aFile) {
                $subValue = strstr($aFile, $filePrefix);
                $apiClassPath = str_replace(array($filePrefix, '.php'), array('', ''), $subValue);
                $apiClassShortName = str_replace(D_S, '_', $apiClassPath) ;
                $apiClassName = '\\' . $namespace . 'Api\\' . str_replace('_', '\\', $apiClassShortName);

                if (!class_exists($apiClassName)) {
                    continue;
                }

                //  左菜单的标题
                $ref        = new \ReflectionClass($apiClassName);
                $title      = "//请检测接口服务注释($apiClassName)";
                $desc       = '//请使用@desc 注释';
                $isClassIgnore = false; // 是否屏蔽此接口类
                $docComment = $ref->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", $docComment);
                    $comment       = trim($docCommentArr[1]);
                    $title         = trim(substr($comment, strpos($comment, '*') + 1));
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

                $allApiS[$apiClassShortName]['title'] = $title;
                $allApiS[$apiClassShortName]['desc']  = $desc;
                $allApiS[$apiClassShortName]['methods'] = array();

                $method = array_diff(get_class_methods($apiClassName), $allPhalApiApiMethods);
                sort($method);
                foreach ($method as $mValue) {
                    $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                    if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
                        continue;
                    }

                    $title      = '//请检测函数注释';
                    $desc       = '//请使用@desc 注释';
                    $isMethodIgnore = false;
                    $docComment = $rMethod->getDocComment();
                    if ($docComment !== false) {
                        $docCommentArr = explode("\n", $docComment);
                        $comment       = trim($docCommentArr[1]);
                        $title         = trim(substr($comment, strpos($comment, '*') + 1));

                        foreach ($docCommentArr as $comment) {
                            $pos = stripos($comment, '@desc');
                            if ($pos !== false) {
                                $desc = substr($comment, $pos + 5);
                            }

                            if (stripos($comment, '@ignore') !== false) {
                                $isMethodIgnore = true;
                            }
                        }
                    }

                    if ($isMethodIgnore) {
                        continue;
                    }

                    $service                                           = trim($namespace, '\\') . '.' . $apiClassShortName . '.' . ucfirst($mValue);
                    $allApiS[$apiClassShortName]['methods'][$service] = array(
                        'service' => $service,
                        'title'   => $title,
                        'desc'    => $desc,
                    );
                }
            }
        }

        // 运行模式
        $env = (PHP_SAPI == 'cli') ? TRUE : FALSE;
        $webRoot = '';
        if ($env) {
            $trace = debug_backtrace();
            $listFilePath = $trace[0]['file'];
            $webRoot = substr($listFilePath, 0, strrpos($listFilePath, D_S));
        }

        // 主题风格，fold = 折叠，expand = 展开
        $theme = isset($_GET['type']) ? $_GET['type'] : 'fold';
        global $argv;
        if ($env) {
            $theme = isset($argv[1]) ? $argv[1] : 'fold';
        }
        if (!in_array($theme, array('fold', 'expand'))) {
            $theme = 'fold';
        }

        //echo json_encode($allApiS) ;
        //字典排列
        ksort($allApiS);

        $projectName = $this->projectName;

        include dirname(__FILE__) . '/api_list_tpl.php';
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

function saveHtml($webRoot, $name, $string){
    $dir = $webRoot . D_S . 'docs';
    if (!is_dir ( $dir)){
        mkdir ( $dir);
    }
    $handle = fopen ( $dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite ( $handle, $string);
    fclose ( $handle);
}

