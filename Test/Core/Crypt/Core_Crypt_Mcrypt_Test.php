<?php
/**
 * PhpUnderControl_CoreCryptMcrypt_Test
 *
 * 针对 ./../../../Core/Crypt/Mcrypt.php Core_Crypt_Mcrypt 类的PHPUnit单元测试
 *
 * @author: dogstar 20141210
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('Core_Crypt_Mcrypt')) {
    require dirname(__FILE__) . '/./../../../Core/Crypt/Mcrypt.php';
}

class PhpUnderControl_CoreCryptMcrypt_Test extends PHPUnit_Framework_TestCase
{
    public $coreCryptMcrypt;

    protected function setUp()
    {
        parent::setUp();

        if (!function_exists('mcrypt_module_open')) {
            throw new Exception('function mcrypt_module_open() not exists');
        }

        $this->coreCryptMcrypt = new Core_Crypt_Mcrypt();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testEncrypt
     */ 
    public function testEncrypt()
    {
        $data = 'dogstar test哈哈 ！！~~ ';
        $key = '2014';

        $rs = $this->coreCryptMcrypt->encrypt($data, $key);

        return array($data, $key, $rs);
    }

    /**
     * @depends testEncrypt
     * @group testDecrypt
     */ 
    public function testDecrypt($rsData)
    {
        list($data, $key, $encryptData) = $rsData;

        $rs = $this->coreCryptMcrypt->decrypt($encryptData, $key);

        $this->assertEquals($data, $rs);
    }

    /**
     * @dataProvider provideIv
     */
    public function testWithIV($iv)
    {
        $mcrypt = new Core_Crypt_Mcrypt($iv);

        $data = 'dogstar';
        $key = 'wetime';
        $this->assertEquals($mcrypt->decrypt($mcrypt->encrypt($data, $key), $key), $data);
    }

    public function provideIv()
    {
        return array(
            array(12),
            array('12'),
            array('12345678'),
            array('1234567890'),
            array('&632(jnD'),
            );
    }

    /**
     * @dataProvider provideComplicateData
     */
    public function testWorkWithMoreComplicateData($data)
    {
        $mcrypt = new Core_Crypt_Mcrypt('12345678');
        $key = 'wetime';

        $encryptData = $mcrypt->encrypt($data, $key);

        $decryptData = $mcrypt->decrypt($encryptData, $key);

        $this->assertEquals($data, $decryptData);
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
