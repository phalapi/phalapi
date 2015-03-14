<?php
/**
 * PhpUnderControl_PhalApiCryptRSAPri2Pub_Test
 *
 * 针对 ../../../PhalApi/Crypt/RSA/Pri2Pub.php PhalApi_Crypt_RSA_Pri2Pub 类的PHPUnit单元测试
 *
 * @author: dogstar 20150314
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Crypt_RSA_Pri2Pub')) {
    require dirname(__FILE__) . '/../../../PhalApi/Crypt/RSA/Pri2Pub.php';
}

class PhpUnderControl_PhalApiCryptRSAPri2Pub_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCryptRSAPri2Pub;

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

        $this->phalApiCryptRSAPri2Pub = new PhalApi_Crypt_RSA_Pri2Pub();
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

        $rs = $this->phalApiCryptRSAPri2Pub->encrypt($data, $key);

        $this->assertNotEmpty($rs);

        return $rs;
    }

    /**
     * @group testDecrypt
     */ 
    public function testDecrypt()
    {
        //we need to encrypt the data again, since pubkey is different every time
        $data = $this->phalApiCryptRSAPri2Pub->encrypt('something important here ...', $this->privkey);

        $key = $this->pubkey;

        $rs = $this->phalApiCryptRSAPri2Pub->decrypt($data, $key);

        $this->assertEquals('something important here ...', $rs);
    }

    /**
     * @dataProvider provideComplicateData
     */
    public function testWorkWithMoreComplicateData($data)
    {
        $encryptData = $this->phalApiCryptRSAPri2Pub->encrypt($data, $this->privkey);

        $decryptData = $this->phalApiCryptRSAPri2Pub->decrypt($encryptData, $this->pubkey);
        $this->assertNotNull($decryptData);
        $this->assertEquals($data, $decryptData);

        $wrongDecryptData = $this->phalApiCryptRSAPri2Pub->decrypt($encryptData, 'whatever');
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
            );
    }

}
