<?php
namespace PhalApi\Tests;

use PhalApi\crypt;
use PhalApi\Cookie\MultiCookie;

/**
 * PhpUnderControl_PhalApiCookieMulti_Test
 *
 * 针对 ../../PhalApi/Cookie/Multi.php PhalApi_Cookie_Multi 类的PHPUnit单元测试
 *
 * @author: dogstar 20150411
 */

class PhpUnderControl_PhalApiCookieMulti_Test extends \PHPUnit_Framework_TestCase
{
    public $multiCookie;

    protected function setUp()
    {
        parent::setUp();

        $config = array('crypt' => new CookieCryptMock(), 'key' => 'aha~');
        $this->multiCookie = new MultiCookie($config);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $name = NULL;

        $rs = $this->multiCookie->get($name);

        $this->assertTrue(is_array($rs));

    }

    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $name = 'aEKey';
        $value = '2015';
        $expire = $_SERVER['REQUEST_TIME'] + 10;

        $rs = @$this->multiCookie->set($name, $value, $expire);

        //remember
        $this->assertEquals($value, $this->multiCookie->get($name));
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $name = 'aEKey';
        $value = '2015';
        $expire = $_SERVER['REQUEST_TIME'] + 10;

        $rs = @$this->multiCookie->set($name, $value, $expire);

        $this->assertNotEmpty($this->multiCookie->get($name));

        $rs = @$this->multiCookie->delete($name);

        $this->assertNull($this->multiCookie->get($name));
    }

    public function testReset()
    {
        $multi = new MultiCookie(array('crypt' => 'WRONG'));

        //$multi->set('a_array', array('name' => 'phalapi'));

        $_COOKIE['a_array'] = array('name' => 'phalapi');

        $rs = $multi->get('a_array');

        $this->assertEquals(array('name' => 'phalapi'), $rs);
    }
}

class CookieCryptMock implements Crypt {

    public function encrypt($data, $key) {
        return base64_encode($data);
    }

    public function decrypt($data, $key) {
        return base64_decode($data);
    }
}
