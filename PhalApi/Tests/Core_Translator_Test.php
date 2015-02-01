<?php
/**
 * PhpUnderControl_CoreTranslator_Test
 *
 * 针对 ../Core/Translator.php Core_Translator 类的PHPUnit单元测试
 *
 * @author: dogstar 20150201
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('Core_Translator')) {
    require dirname(__FILE__) . '/../Core/Translator.php';
}

class PhpUnderControl_CoreTranslator_Test extends PHPUnit_Framework_TestCase
{
    public $coreTranslator;

    protected function setUp()
    {
        parent::setUp();

        $this->coreTranslator = new Core_Translator();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        Core_Translator::setLanguage('zh_cn');

        $this->assertEquals('用户不存在', Core_Translator::get('user not exists'));

        $this->assertEquals('PHPUnit您好，欢迎使用PhalApi！', Core_Translator::get('Hello {name}, Welcome to use PhalApi!', array('name' => 'PHPUnit')));
    }

    /**
     * @group testSetLanguage
     */ 
    public function testSetLanguage()
    {
        $language = 'en';

        $rs = Core_Translator::setLanguage($language);
    }

    /**
     * @group testFormatVar
     */ 
    public function testFormatVar()
    {
        $name = 'abc';

        $rs = Core_Translator::formatVar($name);

        $this->assertEquals('{abc}', $rs);
    }

}
