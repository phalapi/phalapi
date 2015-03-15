<?php
/**
 * PhpUnderControl_PhalApiCryptRSAPri2Pub_Test
 *
 * 针对 ../../../PhalApi/Crypt/RSA/Pri2Pub.php PhalApi_Crypt_RSA_Pri2Pub 类的PHPUnit单元测试
 *
 * @author: dogstar 20150315
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('PhalApi_Crypt_RSA_Pri2Pub')) {
    require dirname(__FILE__) . '/../../../PhalApi/Crypt/RSA/Pri2Pub.php';
}

class PhpUnderControl_PhalApiCryptRSAPri2Pub_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCryptRSAPri2Pub;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiCryptRSAPri2Pub = new PhalApi_Crypt_RSA_Pri2Pub();
    }

    protected function tearDown()
    {
    }


    public function testHere()
    {
        $keyG = new PhalApi_Crypt_RSA_KeyGenerator();
        $prikey = $keyG->getPriKey();
        $pubkey = $keyG->getPubkey();

        $data = 'something important here ...';

        $encryptData = $this->phalApiCryptRSAPri2Pub->encrypt($data, $prikey);

        $decryptData = $this->phalApiCryptRSAPri2Pub->decrypt($encryptData, $pubkey);

        $this->assertEquals($data, $decryptData);
    }
}
