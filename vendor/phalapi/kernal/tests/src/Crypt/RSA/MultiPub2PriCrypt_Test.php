<?php
namespace PhalApi\Tests;

use PhalApi\Crypt\RSA\MultiPub2PriCrypt;
use PhalApi\Crypt\RSA\KeyGenerator;

/**
 * PhpUnderControl_PhalApiCryptRSAPub2Pri_Test
 *
 * 针对 ../../../PhalApi/Crypt/RSA/Pub2Pri.php PhalApi_Crypt_RSA_Pub2Pri 类的PHPUnit单元测试
 *
 * @author: dogstar 20150314
 */

require_once dirname(__FILE__) . '/MultiPri2PubCrypt_Test.php';

class PhpUnderControl_PhalApiCryptRSAPub2Pri_Test extends PhpUnderControl_PhalApiCryptRSAMultiPri2Pub_Test
{
    protected function setUp()
    {
        parent::setUp();

		// 出于测试的方便，这里采用了相同的数据进行测试，只是在使用Pub2Pri方式时，需要互调一下key位置
		// 有点奇怪，我知道 :)
        $keyG = new KeyGenerator();
        $this->privkey = $keyG->getPubKey();
        $this->pubkey = $keyG->getPriKey();

        $this->multiPri2PubCrypt = new MultiPub2PriCrypt();
    }

}
