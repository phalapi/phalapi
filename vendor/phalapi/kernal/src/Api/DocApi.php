<?php

namespace PhalApi\Api;

defined('D_S') || define('D_S', DIRECTORY_SEPARATOR);

/**
 * 文档接口
 *
 * @author liyujiang<1032694760@qq.com>
 * @since 2020.10.23
 */
class DocApi extends \PhalApi\Api
{

    public function getRules()
    {
        return array(
            'list' => array(),
            'desc' =>  array(
                'api' => array('name' => 'api', 'require' => true, 'desc' => \PhalApi\T('Service name such as `App.Demo.Index`'),),
            ),
        );
    }

    /**
     * 列出已实现的所有接口
     *
     * @return string name 接口大类
     * @return array methods 接口列表
     */
    public function list()
    {
        // 以下代码改自 PhalApi\Helper\ApiList.php
        $composerJson = file_get_contents(API_ROOT . D_S . 'composer.json');
        $composerArr = json_decode($composerJson, TRUE);

        $psr4 = isset($composerArr['autoload']['psr-4']) ? $composerArr['autoload']['psr-4'] : array();
        // 检测通用项目API
        $root = @$psr4[''];
        if (!empty($root)) { // 其它通用项目检测
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

        // 扫描接口文件
        $apiServiceNamespace = array();
        foreach ($psr4 as $namespace => $srcPath) {
            if (!is_string($srcPath) || strpos($srcPath, 'src') === FALSE) {
                continue;
            }

            // 列出Api目录下的所有文件
            $files = listDir(API_ROOT . D_S . $srcPath . D_S . 'Api');
            $filePrefix = rtrim($srcPath, D_S) . D_S . 'Api' . D_S;

            $apiServiceClass = array();
            foreach ($files as $aFile) {
                $subValue = strstr($aFile, $filePrefix);
                $apiClassPath = str_replace(array($filePrefix, '.php'), array('', ''), $subValue);
                $apiClassShortName = str_replace(D_S, '_', $apiClassPath);
                // 构造出接口类的完整类名
                $apiClassName = '\\' . $namespace . 'Api\\' . str_replace('_', '\\', $apiClassShortName);

                if (!class_exists($apiClassName)) {
                    continue;
                }

                $ref = new \ReflectionClass($apiClassName);
                $title = '//' . \PhalApi\T('Please check the class comment {className}', array('className' => $apiClassName));
                $desc = '//' . \PhalApi\T('Please use the `@desc` annotation');
                $isClassIgnore = false; // 是否屏蔽此接口类
                // 反射得到类的文档注释
                $docComment = $ref->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", $docComment);
                    $comment = trim($docCommentArr[1]);
                    // 以文档注释中的第二行作为标题
                    $title = trim(substr($comment, strpos($comment, '*') + 1));
                    foreach ($docCommentArr as $comment) {
                        // 以@desc所在行的内容作为描述
                        $pos = stripos($comment, '@desc');
                        if ($pos !== false) {
                            $desc = trim(substr($comment, $pos + 5));
                        }
                        // 以@ignore作为忽略标志
                        if (stripos($comment, '@ignore') !== false) {
                            $isClassIgnore = true;
                        }
                    }
                }

                if ($isClassIgnore) {
                    continue;
                }


                $method = array_diff(get_class_methods($apiClassName), $allPhalApiApiMethods);
                sort($method);

                $apiServiceMethods = array();
                foreach ($method as $mValue) {
                    $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                    if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
                        continue;
                    }

                    $title = '//' . \PhalApi\T('Please check the function comment');
                    $desc = '//' . \PhalApi\T('Please use the `@desc` annotation');
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
                                $desc = trim(substr($comment, $pos + 5));
                            }
                            if (stripos($comment, '@ignore') !== false) {
                                $isMethodIgnore = true;
                            }
                            $pos = stripos($comment, '@method');
                            if ($pos !== FALSE) {
                                $methods = trim(substr($comment, $pos + 8));
                                continue;
                            }
                        }
                    }

                    if ($isMethodIgnore) {
                        continue;
                    }

                    $service = trim($namespace, '\\') . '.' . $apiClassShortName . '.' . ucfirst($mValue);

                    array_push($apiServiceMethods, array(
                        'name' => $service,
                        'title' => $title,
                        'desc' => $desc,
                        'methods' => $methods,
                    ));
                }

                array_push($apiServiceClass, array(
                    'name' => $apiClassShortName,
                    'title' => $title,
                    'desc' => $desc,
                    'methods' => $apiServiceMethods
                ));
            }

            array_push($apiServiceNamespace, array(
                'name' => rtrim($namespace, '\\'),
                'methods' => $apiServiceClass
            ));
        }

        return $apiServiceNamespace;
    }

    /**
     * 列出某个接口的相关说明
     *
     * @return string name 接口名称
     * @return string title 接口说明
     * @return string desc 更多说明
     * @return array rule 接口参数
     * @return array return 返回结果
     * @return array exception 异常情况
     */
    public function desc()
    {
        //以下代码改自 PhalApi\Helper\ApiDesc.php
        $service = $this->api;
        @list($namespace, $apiName, $actionName) = explode('.', $service);

        // 支持多级命名空间，构造出接口服务的完整类名
        $namespace = str_replace('_', '\\', $namespace);
        $className = '\\' . $namespace . '\\Api\\' . str_replace('_', '\\', ucfirst($apiName));

        $rules = array();
        $returns = array();
        $description = '';
        $descComment = '//' . \PhalApi\T('Please use the `@desc` annotation');
        $methods = '';
        $exceptions = array();

        $pai = \PhalApi\ApiFactory::generateService(FALSE);
        $apiRules = $pai->getApiRules();
        foreach ($apiRules as &$value) {
            if (!isset($value['type'])) {
                $value['type'] = 'string';
            }
            if (!isset($value['default'])) {
                $value['default'] = '';
            }
            ksort($value);
            array_push($rules, $value);
        }

        // 整合需要的类注释，包括父类注释
        $rClass = new \ReflectionClass($className);
        $classDocComment = $rClass->getDocComment();
        while ($parent = $rClass->getParentClass()) {
            if ($parent->getName() == '\\PhalApi\\Api') {
                break;
            }
            $classDocComment = $parent->getDocComment() . "\n" . $classDocComment;
            $rClass = $parent;
        }
        $needClassDocComment = '';
        foreach (explode("\n", $classDocComment) as $comment) {
            if (
                stripos($comment, '@exception') !== FALSE
                || stripos($comment, '@return') !== FALSE
            ) {
                $needClassDocComment .=  "\n" . $comment;
            }
        }

        // 方法注释
        $rMethod = new \ReflectionMethod($className, $actionName);
        $docCommentArr = explode("\n", $needClassDocComment . "\n" . $rMethod->getDocComment());

        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);

            //标题描述
            if (empty($description) && strpos($comment, '@') === FALSE && strpos($comment, '/') === FALSE) {
                $description = trim(substr($comment, strpos($comment, '*') + 1));
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== FALSE) {
                $descComment = trim(substr($comment, $pos + 5));
                continue;
            }

            //@method注释
            $pos = stripos($comment, '@method');
            if ($pos !== FALSE) {
                $methods = trim(substr($comment, $pos + 8));
                continue;
            }

            //@exception注释
            $pos = stripos($comment, '@exception');
            if ($pos !== FALSE) {
                $exArr = explode(' ', trim(substr($comment, $pos + 10)));
                array_push($exceptions, array(
                    'name' => $exArr[0],
                    'desc' => $exArr[1]
                ));
                continue;
            }

            //@return注释
            $pos = stripos($comment, '@return');
            if ($pos === FALSE) {
                continue;
            }

            $returnCommentArr = explode(' ', trim(substr($comment, $pos + 8)));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';    //可选的字段说明
            } else {
                //兼容处理有空格的注释
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }
            array_push($returns, array(
                'type' => $returnCommentArr[0],
                'name' => $returnCommentArr[1],
                'desc' => $returnCommentArr[2]
            ));
        }
        return array(
            'name' => $service,
            'title' => $description,
            'desc' => $descComment,
            'rule' => $rules,
            'method' => $methods,
            'return' => $returns,
            'exception' => $exceptions,
        );
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
