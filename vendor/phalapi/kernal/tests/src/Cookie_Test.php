<?php
namespace PhalApi\Tests;

use PhalApi\Cookie;

/**
 * PhpUnderControl_PhalApiCookie_Test
 *
 * 针对 ../../../PhalApi/PhalApi/Cookie.php PhalApi_Cookie 类的PHPUnit单元测试
 *
 * @author: dogstar 20150411
 */

class PhpUnderControl_PhalApiCookie_Test extends \PHPUnit_Framework_TestCase
{
    public $cookie;

    protected function setUp()
    {
        parent::setUp();

        $this->cookie = new Cookie();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = NULL;

        $rs = $this->cookie->get($key);

        $this->assertTrue(is_array($rs));

        $this->assertNull($this->cookie->get('noThisKey'));

        $_COOKIE['aKey'] = 'phalapi';
        $key = 'aKey';
        $this->assertEquals('phalapi', $this->cookie->get($key));
    }

    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'bKey';
        $value = '2015';

        $rs = @$this->cookie->set($key, $value);

        //should not get in this time, but next time
        $this->assertNull($this->cookie->get($key));
    }

}
