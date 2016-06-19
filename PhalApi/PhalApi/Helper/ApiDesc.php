<?php
/**
 * PhalApi_Helper_ApiDesc - 在线接口描述查看 - 辅助类
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-05-30
 */

class PhalApi_Helper_ApiDesc {

    public function render() {
        $service = DI()->request->get('service', 'Default.Index');

        $rules = array();
        $returns = array();
        $description = '';
        $descComment = '//请使用@desc 注释';

        $typeMaps = array(
            'string' => '字符串',
            'int' => '整型',
            'float' => '浮点型',
            'boolean' => '布尔型',
            'date' => '日期',
            'array' => '数组',
            'fixed' => '固定值',
            'enum' => '枚举类型',
            'object' => '对象',
        );

        try {
            $api = PhalApi_ApiFactory::generateService(false);
            $rules = $api->getApiRules();
        } catch (PhalApi_Exception $ex){
            $service .= ' - ' . $ex->getMessage();
            include dirname(__FILE__) . '/api_desc_tpl.php';
            return;
        }

        list($className, $methodName) = explode('.', $service);
        $className = 'Api_' . $className;

        $rMethod = new ReflectionMethod($className, $methodName);
        $docComment = $rMethod->getDocComment();
        $docCommentArr = explode("\n", $docComment);

        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);

            //标题描述
            if (empty($description) && strpos($comment, '@') === false && strpos($comment, '/') === false) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }

            //@return注释
            $pos = stripos($comment, '@return');
            if ($pos === false) {
                continue;
            }

            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';	//可选的字段说明
            } else {
                //兼容处理有空格的注释
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }

            $returns[] = $returnCommentArr; 
        }

        include dirname(__FILE__) . '/api_desc_tpl.php';
    }
}
