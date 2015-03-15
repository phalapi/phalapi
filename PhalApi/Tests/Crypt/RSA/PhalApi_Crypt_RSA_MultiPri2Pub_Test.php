<?php
/**
 * PhpUnderControl_PhalApiCryptRSAMultiPri2Pub_Test
 *
 * 针对 ../../../PhalApi/Crypt/RSA/MultiPri2Pub.php PhalApi_Crypt_RSA_MultiPri2Pub 类的PHPUnit单元测试
 *
 * @author: dogstar 20150314
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Crypt_RSA_MultiPri2Pub')) {
    require dirname(__FILE__) . '/../../../PhalApi/Crypt/RSA/MultiPri2Pub.php';
}

class PhpUnderControl_PhalApiCryptRSAMultiPri2Pub_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCryptRSAMultiPri2Pub;

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

        $keyG = new PhalApi_Crypt_RSA_KeyGenerator();
        $this->privkey = $keyG->getPriKey();
        $this->pubkey = $keyG->getPubKey();

        $this->phalApiCryptRSAMultiPri2Pub = new PhalApi_Crypt_RSA_MultiPri2Pub();
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

        $rs = $this->phalApiCryptRSAMultiPri2Pub->encrypt($data, $key);

        $this->assertNotEmpty($rs);

        return $rs;
    }

    /**
     * @group testDecrypt
     */ 
    public function testDecrypt()
    {
        //we need to encrypt the data again, since pubkey is different every time
        $data = $this->phalApiCryptRSAMultiPri2Pub->encrypt('something important here ...', $this->privkey);

        $key = $this->pubkey;

        $rs = $this->phalApiCryptRSAMultiPri2Pub->decrypt($data, $key);

        $this->assertEquals('something important here ...', $rs);
    }

    /**
     * demo
     */
    public function testDecryptAfterEncrypt()
    {
        $keyG = new PhalApi_Crypt_RSA_KeyGenerator();
        $privkey = $keyG->getPriKey();
        $pubkey = $keyG->getPubKey();

        DI()->crypt = new PhalApi_Crypt_RSA_MultiPri2Pub();

        $data = 'AHA! I have $2.22 dollars!';

        $encryptData = DI()->crypt->encrypt($data, $privkey);

        $decryptData = DI()->crypt->decrypt($encryptData, $pubkey);

        $this->assertEquals($data, $decryptData);
    }

    /**
     * @dataProvider provideComplicateData
     */
    public function testWorkWithMoreComplicateData($data)
    {
        $encryptData = $this->phalApiCryptRSAMultiPri2Pub->encrypt($data, $this->privkey);

        $decryptData = $this->phalApiCryptRSAMultiPri2Pub->decrypt($encryptData, $this->pubkey);
        $this->assertNotNull($decryptData);
        $this->assertEquals($data, $decryptData);

        $wrongDecryptData = $this->phalApiCryptRSAMultiPri2Pub->decrypt($encryptData, 'whatever');
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
