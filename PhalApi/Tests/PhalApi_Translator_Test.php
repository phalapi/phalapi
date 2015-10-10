<?php
/**
 * PhpUnderControl_PhalApiTranslator_Test
 *
 * 针对 ../PhalApi/Translator.php PhalApi_Translator 类的PHPUnit单元测试
 *
 * @author: dogstar 20150201
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Translator')) {
    require dirname(__FILE__) . '/../PhalApi/Translator.php';
}

class PhpUnderControl_PhalApiTranslator_Test extends PHPUnit_Framework_TestCase
{
    public $coreTranslator;

    protected function setUp()
    {
        parent::setUp();

        $this->coreTranslator = new PhalApi_Translator();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        PhalApi_Translator::setLanguage('zh_cn');

        $this->assertEquals('用户不存在', PhalApi_Translator::get('user not exists'));

        $this->assertEquals('PHPUnit您好，欢迎使用PhalApi！', PhalApi_Translator::get('Hello {name}, Welcome to use PhalApi!', array('name' => 'PHPUnit')));

        $this->assertEquals('PhalApi 我爱你', T('{0} I love you', array('PhalApi')));
        $this->assertEquals('PhalApi 我爱你因为no reasons', T('{0} I love you because {1}', array('PhalApi', 'no reasons')));
    }

    /**
     * @group testSetLanguage
     */ 
    public function testSetLanguage()
    {
        $language = 'en';

        $rs = PhalApi_Translator::setLanguage($language);
    }

    /**
     * @group testFormatVar
     */ 
    public function testFormatVar()
    {
        $name = 'abc';

        $rs = PhalApi_Translator::formatVar($name);

        $this->assertEquals('{abc}', $rs);
    }

    public function testAddMessage() 
    {
        PhalApi_Translator::setLanguage('zh_cn');
        PhalApi_Translator::addMessage(dirname(__FILE__) . '/Data');

        $this->assertEquals('this is a good way', PhalApi_Translator::get('test'));
    }

    public function testGetWithNoLanguageSet()
    {
        PhalApi_Translator_Mock::setLanguageNameSimple(null);

        $rs = T('test');

        PhalApi_Translator::setLanguage('zh_cn');
    }
}

class PhalApi_Translator_Mock extends PhalApi_Translator {

    public static function setLanguageNameSimple($lan) {
        PhalApi_Translator::$message = null;
    }
}
