<?php
defined('D_S') || define('D_S', DIRECTORY_SEPARATOR);

class PhalApi_Helper_ApiList extends PhalApi_Helper_ApiOnline {

    public function render($apiDirName, $libraryPaths) {
        // 处理项目
        DI()->loader->addDirs($apiDirName);
        $files = listDir(API_ROOT . D_S . $apiDirName. D_S . 'Api');

        // 追加处理扩展类库
        foreach ($libraryPaths as $aPath) {
            $toAddDir = str_replace('/', D_S, $aPath);
            DI()->loader->addDirs($toAddDir);

            $toListDir = API_ROOT . D_S . $toAddDir . D_S . 'Api';
            $aLibFiles = listDir($toListDir);

            $files = array_merge($files, $aLibFiles);
        }

        // 待排除的方法
        $allPhalApiApiMethods = get_class_methods('PhalApi_Api');

        // 扫描接口文件
        $allApiS = array();
        $errorMessage = '';

        foreach ($files as $value) {
            $value    = realpath($value);
            $subValue = substr($value, strpos($value, D_S . 'Api' . D_S) + 1);
            //支持多层嵌套，不限级
            $arr                = explode(D_S, $subValue);
            $subValue           = implode(D_S, $arr);
            $apiServer          = str_replace(array(D_S, '.php'), array('_', ''), $subValue);
            $apiServerShortName = substr($apiServer, 4);

            if (!class_exists($apiServer)) {
                continue;
            }

            // 检测文件路径的合法性
            if (ucfirst(substr($apiServer, 4)) != substr($apiServer, 4)) {
                $errorMessage .= $apiServer . ' 类文件首字母必须大写！<br/>';
            }


            //  左菜单的标题
            $ref        = new ReflectionClass($apiServer);
            $title      = "//请检测接口服务注释($apiServer)";
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

            $allApiS[$apiServerShortName]['title'] = $title;
            $allApiS[$apiServerShortName]['desc']  = $desc;
            $allApiS[$apiServerShortName]['methods'] = array();

            $method = array_diff(get_class_methods($apiServer), $allPhalApiApiMethods);
            sort($method);
            foreach ($method as $mValue) {
                $rMethod = new Reflectionmethod($apiServer, $mValue);
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

                $service                                           = $apiServerShortName . '.' . ucfirst($mValue);
                $allApiS[$apiServerShortName]['methods'][$service] = array(
                    'service' => $service,
                    'title'   => $title,
                    'desc'    => $desc,
                );
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
    $dir = $webRoot . D_S . 'doc';
    if (!is_dir ( $dir)){
        mkdir ( $dir);
    }
    $handle = fopen ( $dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite ( $handle, $string);
    fclose ( $handle);
}

