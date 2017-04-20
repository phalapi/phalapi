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
 * PhalApi_Helper_ApiDesc - Online API Detail Document - Helper
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-05-30
 */

class PhalApi_Helper_ApiDesc {

    public function render() {
        $service = DI()->request->getService();

        $rules = array();
        $returns = array();
        $description = '';
        $descComment = '// please use @desc annotation';
        $exceptions = array();

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

            // Title Desc
            if (empty($description) && strpos($comment, '@') === false && strpos($comment, '/') === false) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            // @desc annotation
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }

            // @exception annotation
            $pos = stripos($comment, '@exception');
            if ($pos !== false) {
                $exceptions[] = explode(' ', trim(substr($comment, $pos + 10)));
                continue;
            }

            // @return annotation
            $pos = stripos($comment, '@return');
            if ($pos === false) {
                continue;
            }

            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            // filter empty value in the array, and return the value wait to display
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';	// optional desc
            } else {
                // works with much more blank space
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }

            $returns[] = $returnCommentArr; 
        }

        include dirname(__FILE__) . '/api_desc_tpl.php';
    }
}
