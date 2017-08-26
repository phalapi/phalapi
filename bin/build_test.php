<?php
/**
 * 单元测试骨架代码自动生成脚本
 * 主要是针对当前项目系列生成相应的单元测试代码，提高开发效率
 *
 * 用法：
 * Usage: php ./build_test.php <file_path> <class_name> [bootstrap] [author = dogstar]
 *
 * 1、针对全部public的函数进行单元测试
 * 2、可根据@testcase注释自动生成测试用例
 *
 * 备注：另可使用phpunit-skelgen进行骨架代码生成
 *
 * @author: dogstar 20170708
 * @version: 5.1.1
 */

if ($argc < 3) {
    echo "\n";
    echo colorfulString("Usage:\n", 'WARNING');
    echo "    php $argv[0] <file_path> <class_name> [bootstrap] [author]\n";
    echo "\n";

    echo colorfulString("Options:\n", 'WARNING');
    echo colorfulString('    file_path', 'NOTE'), "         Require. Path to the PHP source code file\n";
    echo colorfulString('    class_name', 'NOTE'), "        Require. The class name need to be tested\n";
    echo colorfulString('    bootstrap', 'NOTE'), "         NOT require. Path to the bootsrap file, usually is test_env.php\n";
    echo colorfulString('    author', 'NOTE'), "            NOT require. Your great name here, default is dogstar\n";
    echo "\n";

    echo colorfulString("Demo:\n", 'WARNING');
    echo "    $argv[0] ./Demo.php Demo > Demo_Test.php\n";
    echo "    $argv[0] ./Demo.php Demo > Demo_Test.php\n";
    echo "    $argv[0] ./src/Request.php PhalApi\\\\Reqeust > Request_Test.php\n";
    echo "\n";

    echo colorfulString("Tips:\n", 'WARNING');
    echo "    This will output the code directly, you can save them to test file like with _Test.php suffix.\n";
    echo "\n";

    die();
}

$filePath = $argv[1];
$className = $argv[2];
$bootstrap = isset($argv[3]) ? $argv[3] : null;
$author = isset($argv[4]) ? $argv[4] : 'dogstar';

// 尝试加载composer autoload
$autoloadFiles = array(
    dirname(__FILE__) . '/../vendor/autoload.php',
    dirname(__FILE__) . '/../../../vendor/autoload.php',
);
foreach ($autoloadFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

// 引入启动文件
if (!empty($bootstrap)) {
    require_once $bootstrap;
}

// 引入源代码
require_once $filePath;

if (!class_exists($className)) {
    echo colorfulString("Error: cannot find class($className). \n\n", 'FAILURE');
    die();
}

$reflector = new ReflectionClass($className);

$methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

date_default_timezone_set('Asia/Shanghai');
$objName = lcfirst(str_replace(array('_', '\\'), array('', ''), $className));

/** ------------------- 生成通用的单元测试代码 ------------------ **/

$code = "<?php

";

if (file_exists(dirname(__FILE__) . '/test_env.php')) {
    $code .= "require_once dirname(__FILE__) . '/bootstrap.php';
";
} else {
    $code .= "//require_once dirname(__FILE__) . '/bootstrap.php';
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
if (!class_exists('" . (strpos($className, '\\') !== false ? str_replace('\\', '\\\\', $className) : $className) . "')) {
    require dirname(__FILE__) . '/$filePath';
}

/**
 * PhpUnderControl_" . str_replace('_', '', $className) . "_Test
 *
 * 针对 $filePath $className 类的PHPUnit单元测试
 *
 * @author: $author " . date('Ymd') . "
 */

class PhpUnderControl_" . str_replace(array('_', '\\'), array('', ''), $className) . "_Test extends \PHPUnit_Framework_TestCase
{
    public \$$objName;

    protected function setUp()
    {
        parent::setUp();

        \$this->$objName = $initWay;
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getSqls());
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
        } else if ($default === null) {
            $default = 'null';
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

            //去掉@testcase和期望的结果
            array_shift($returnCommentArr);
            array_shift($returnCommentArr);

            $callParamStrInCase = !empty($returnCommentArr) ? implode(' ', $returnCommentArr) : '';

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

function colorfulString($text, $type = NULL) {
    $colors = array(
        'WARNING'   => '1;33',
        'NOTE'      => '1;36',
        'SUCCESS'   => '1;32',
        'FAILURE'   => '1;35',
    );

    if (empty($type) || !isset($colors[$type])){
        return $text;
    }

    return "\033[" . $colors[$type] . "m" . $text . "\033[0m";
}

