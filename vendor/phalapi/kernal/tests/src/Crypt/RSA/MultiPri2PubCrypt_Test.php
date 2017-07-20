<?php
namespace PhalApi\Tests;

use PhalApi\Crypt\RSA\MultiPri2PubCrypt;
use PhalApi\Crypt\RSA\KeyGenerator;

/**
 * PhpUnderControl_PhalApiCryptRSAMultiPri2Pub_Test
 *
 * 针对 ../../../PhalApi/Crypt/RSA/MultiPri2Pub.php PhalApi_Crypt_RSA_MultiPri2Pub 类的PHPUnit单元测试
 *
 * @author: dogstar 20150314
 */

class PhpUnderControl_PhalApiCryptRSAMultiPri2Pub_Test extends \PHPUnit_Framework_TestCase
{
    public $multiPri2PubCrypt;

    public $privkey;

    public $pubkey;

    protected function setUp()
    {
        parent::setUp();

        /**
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $privkey);
        $this->privkey = $privkey;

        $pubkey = openssl_pkey_get_details($res);
        $this->pubkey = $pubkey["key"];
         */

        $keyG = new KeyGenerator();
        $this->privkey = $keyG->getPriKey();
        $this->pubkey = $keyG->getPubKey();

        $this->multiPri2PubCrypt = new MultiPri2PubCrypt();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testEncrypt
     */ 
    public function testEncrypt()
    {
        $data = 'something important here ...';
        $key = $this->privkey;

        $rs = $this->multiPri2PubCrypt->encrypt($data, $key);

        $this->assertNotEmpty($rs);

        return $rs;
    }

    /**
     * @group testDecrypt
     */ 
    public function testDecrypt()
    {
        //we need to encrypt the data again, since pubkey is different every time
        $data = $this->multiPri2PubCrypt->encrypt('something important here ...', $this->privkey);

        $key = $this->pubkey;

        $rs = $this->multiPri2PubCrypt->decrypt($data, $key);

        $this->assertEquals('something important here ...', $rs);
    }

    /**
     * demo
     */
    public function testDecryptAfterEncrypt()
    {
        $keyG = new KeyGenerator();
        $privkey = $keyG->getPriKey();
        $pubkey = $keyG->getPubKey();

        \PhalApi\DI()->crypt = new MultiPri2PubCrypt();

        $data = 'AHA! I have $2.22 dollars!';

        $encryptData = \PhalApi\DI()->crypt->encrypt($data, $privkey);

        $decryptData = \PhalApi\DI()->crypt->decrypt($encryptData, $pubkey);

        $this->assertEquals($data, $decryptData);
    }

    /**
     * @dataProvider provideComplicateData
     */
    public function testWorkWithMoreComplicateData($data)
    {
        $encryptData = $this->multiPri2PubCrypt->encrypt($data, $this->privkey);

        $decryptData = $this->multiPri2PubCrypt->decrypt($encryptData, $this->pubkey);
        $this->assertNotNull($decryptData);
        $this->assertEquals($data, $decryptData);

        $wrongDecryptData = $this->multiPri2PubCrypt->decrypt($encryptData, 'whatever');
        $this->assertNotSame($data, $wrongDecryptData);
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
            array(json_encode(array('name' => 'dogstar', 'ext' => '来点中文行不行？'))),
            array('something important here ...'),
            array(str_repeat('something long long here ...', 130)),
            );
    }

}
