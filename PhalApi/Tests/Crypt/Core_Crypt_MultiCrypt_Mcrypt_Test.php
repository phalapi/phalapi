<?php
/**
 * PhpUnderControl_CoreMultiCryptMcrypt_Test
 *
 * 针对 ../../Core/Crypt/MultiMcrypt.php Core_Crypt_MultiMcrypt 类的PHPUnit单元测试
 *
 * @author: dogstar 20141211
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('Core_Crypt_MultiMcrypt')) {
    require dirname(__FILE__) . '/../../Core/Crypt/MultiMcrypt.php';
}

class PhpUnderControl_CoreMultiCryptMcrypt_Test extends PHPUnit_Framework_TestCase
{
    public $coreMultiCryptMcrypt;

    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('mcrypt_module_open')) {
            throw new Exception('function mcrypt_module_open() not exists');
        }

        $this->coreMultiCryptMcrypt = new Core_Crypt_MultiMcrypt('12345678');
    }

    protected function tearDown()
    {
    }


    /**
     * @group testEncrypt
     */ 
    public function testEncrypt()
    {
        $data = 'haha~';
        $key = '123';

        $rs = $this->coreMultiCryptMcrypt->encrypt($data, $key);
    }

    /**
     * @group testDecrypt
     */ 
    public function testDecrypt()
    {
        $data = 'haha~';
        $key = '123';

        $rs = $this->coreMultiCryptMcrypt->decrypt($data, $key);
    }

    public function testMixed()
    {
        $data = 'haha!哈哈！';
        $key = md5('123');

        $encryptData = $this->coreMultiCryptMcrypt->encrypt($data, $key);

        $decryptData = $this->coreMultiCryptMcrypt->decrypt($encryptData, $key);

        $this->assertEquals($data, $decryptData);
    }

    /**
     * @dataProvider provideComplicateData
     */
    public function testWorkWithMoreComplicateData($data)
    {
        $key = 'wetime';

        $encryptData = $this->coreMultiCryptMcrypt->encrypt($data, $key);

        $decryptData = $this->coreMultiCryptMcrypt->decrypt($encryptData, $key);

        $this->assertSame($data, $decryptData);
    }

    public function provideComplicateData()
    {
        return array(
            array(''),
            array(' '),
            array('0'),
            array(0),
            array(1),
            array('12#d_'),
            array(12345678),
            array('来点中文行不行？'),
            array('843435Jhe*&混合'),
            );
    }
}
