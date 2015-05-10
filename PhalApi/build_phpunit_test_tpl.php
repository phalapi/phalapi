<?php
/**
 * 单元测试骨架代码自动生成脚本
 * 主要是针对当前项目系列生成相应的单元测试代码，提高开发效率
 *
 * 用法：
 * Usage: php ./build_phpunit_test_tpl.php <file_path> <class_name> [bootstrap] [author = dogstar]
 *
 * 1、针对全部public的函数进行单元测试
 * 2、各个函数对应返回格式测试与业务数据测试
 * 3、源文件加载（在没有自动加载的情况下）
 *
 * 备注：另可使用phpunit-skelgen进行骨架代码生成
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2015-01-08
 * @version: 4.0.0
 */

if ($argc < 3) {
    echo "
Usage: 
        php $argv[0] <file_path> <class_name> [bootstrap] [author = dogstar]

Demo:
        php ./build_phpunit_test_tpl.php ./Demo.php Demo > Demo_Test.php
        
";
    die();
}

$filePath = $argv[1];
$className = $argv[2];
$bootstrap = isset($argv[3]) ? $argv[3] : NULL;
$author = isset($argv[4]) ? $argv[4] : 'dogstar';

if (!empty($bootstrap)) {
    require $bootstrap;
}

require $filePath;

if (!class_exists($className)) {
    die("Error: cannot find class($className). \n");
}

$reflector = new ReflectionClass($className);

$methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

date_default_timezone_set('Asia/Shanghai');
$objName = lcfirst(str_replace('_', '', $className));

$code = "<?php
/**
 * PhpUnderControl_" . str_replace('_', '', $className) . "_Test
 *
 * 针对 $filePath $className 类的PHPUnit单元测试
 *
 * @author: $author " . date('Ymd') . "
 */

";

if (true || file_exists(dirname(__FILE__) . '/test_env.php')) {
    $code .= "require_once dirname(__FILE__) . '/test_env.php';
";
}

$initWay = "new $className()";
if (method_exists($className, '__construct')) {
    $constructMethod = new ReflectionMethod($className, '__construct');
    if (!$constructMethod->isPublic()) {
        if (is_callable(array($className, 'getInstance'))) {
            $initWay = "$className::getInstance()";
        } else if(is_callable(array($className, 'newInstance'))) {
            $initWay = "$className::newInstance()";
        } else {
            $initWay = 'NULL';
        }
    }
}

$code .= "
if (!class_exists('$className')) {
    require dirname(__FILE__) . '/$filePath';
}

class PhpUnderControl_" . str_replace('_', '', $className) . "_Test extends PHPUnit_Framework_TestCase
{
    public \$$objName;

    protected function setUp()
    {
        parent::setUp();

        \$this->$objName = $initWay;
    }

    protected function tearDown()
    {
    }

";

foreach ($methods as $method) {
    if($method->class != $className) continue;

    $fun = $method->name;
    $Fun = ucfirst($fun);

    if (strlen($Fun) > 2 && substr($Fun, 0, 2) == '__') continue;

    $rMethod = new ReflectionMethod($className, $method->name);
    $params = $rMethod->getParameters();
    $isStatic = $rMethod->isStatic();
    $isConstructor = $rMethod->isConstructor();

    if($isConstructor) continue;

    $initParamStr = '';
    $callParamStr = '';
    foreach ($params as $param) {
        $default = '';

        $rp = new ReflectionParameter(array($className, $fun), $param->name);
        if ($rp->isOptional()) {
            $default = $rp->getDefaultValue();
        }
        if (is_string($default)) {
            $default = "'$default'";
        } else if (is_array($default)) {
            $default = var_export($default, true);
        } else if (is_bool($default)) {
            $default = $default ? 'true' : 'false';
        } else if ($default === NULL) {
            $default = 'NULL';
        } else {
            $default = "''";
        }

        $initParamStr .= "
        \$" . $param->name . " = $default;";
        $callParamStr .= '$' . $param->name . ', ';
    }
    $callParamStr = empty($callParamStr) ? $callParamStr : substr($callParamStr, 0, -2);

    /** ------------------- 根据@return对结果类型的简单断言 ------------------ **/
    $returnAssert = '';

    $docComment = $rMethod->getDocComment();
    $docCommentArr = explode("\n", $docComment);
    foreach ($docCommentArr as $comment) {
        if (strpos($comment, '@return') == false) {
            continue;
        }
        $returnCommentArr = explode(' ', strrchr($comment, '@return'));
        if (count($returnCommentArr) >= 2) {
            switch (strtolower($returnCommentArr[1])) {
            case 'bool':
            case 'boolean':
                $returnAssert = '$this->assertTrue(is_bool($rs));';
                break;
            case 'int':
                $returnAssert = '$this->assertTrue(is_int($rs));';
                break;
            case 'integer':
                $returnAssert = '$this->assertTrue(is_integer($rs));';
                break;
            case 'string':
                $returnAssert = '$this->assertTrue(is_string($rs));';
                break;
            case 'object':
                $returnAssert = '$this->assertTrue(is_object($rs));';
                break;
            case 'array':
                $returnAssert = '$this->assertTrue(is_array($rs));';
                break;
            case 'float':
                $returnAssert = '$this->assertTrue(is_float($rs));';
                break;
            }

            break;
        }
    }

    /** ------------------- 基本的单元测试代码生成 ------------------ **/
    $code .= "
    /**
     * @group test$Fun
     */ 
    public function test$Fun()
    {"
    . (empty($initParamStr) ? '' : "$initParamStr\n") 
    . "\n        "
    . ($isStatic ? "\$rs = $className::$fun($callParamStr);" : "\$rs = \$this->$objName->$fun($callParamStr);") 
    . (empty($returnAssert) ? '' : "\n\n        " . $returnAssert . "\n") 
    . "
    }
";

    /** ------------------- 根据@testcase 生成测试代码 ------------------ **/
    $caseNum = 0;
    foreach ($docCommentArr as $comment) {
        if (strpos($comment, '@testcase') == false) {
            continue;
        }

        $returnCommentArr = explode(' ', strrchr($comment, '@testcase'));
        if (count($returnCommentArr) > 1) {
            $expRs = $returnCommentArr[1];
            $callParamStrInCase = isset($returnCommentArr[2]) ? $returnCommentArr[2] : '';

            $code .= "
    /**
     * @group test$Fun
     */ 
    public function test{$Fun}Case{$caseNum}()
    {"
        . "\n        "
        . ($isStatic ? "\$rs = $className::$fun($callParamStrInCase);" : "\$rs = \$this->$objName->$fun($callParamStrInCase);") 
        . "\n\n        \$this->assertEquals({$expRs}, \$rs);" 
        . "
    }
";
            $caseNum ++;

        }

    }

}

$code .= "
}";

echo $code;
echo "\n";
