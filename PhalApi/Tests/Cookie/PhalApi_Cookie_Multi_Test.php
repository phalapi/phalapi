<?php
/**
 * PhpUnderControl_PhalApiCookieMulti_Test
 *
 * 针对 ../../PhalApi/Cookie/Multi.php PhalApi_Cookie_Multi 类的PHPUnit单元测试
 *
 * @author: dogstar 20150411
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Cookie_Multi')) {
    require dirname(__FILE__) . '/../../PhalApi/Cookie/Multi.php';
}

class PhpUnderControl_PhalApiCookieMulti_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCookieMulti;

    protected function setUp()
    {
        parent::setUp();

        $config = array('crypt' => new Cookie_Crypt_Mock(), 'key' => 'aha~');
        $this->phalApiCookieMulti = new PhalApi_Cookie_Multi($config);
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

        $rs = $this->phalApiCookieMulti->get($name);

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

        $rs = @$this->phalApiCookieMulti->set($name, $value, $expire);

        //remember
        $this->assertEquals($value, $this->phalApiCookieMulti->get($name));
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $name = 'aEKey';
        $value = '2015';
        $expire = $_SERVER['REQUEST_TIME'] + 10;

        $rs = @$this->phalApiCookieMulti->set($name, $value, $expire);

        $this->assertNotEmpty($this->phalApiCookieMulti->get($name));

        $rs = @$this->phalApiCookieMulti->delete($name);

        $this->assertNull($this->phalApiCookieMulti->get($name));
    }

    public function testReset()
    {
        $multi = new PhalApi_Cookie_Multi(array('crypt' => 'WRONG'));

        //$multi->set('a_array', array('name' => 'phalapi'));

        $_COOKIE['a_array'] = array('name' => 'phalapi');

        $rs = $multi->get('a_array');

        $this->assertEquals(array('name' => 'phalapi'), $rs);
    }


}

class Cookie_Crypt_Mock implements PhalApi_Crypt {

    public function encrypt($data, $key) {
        return base64_encode($data);
    }

    public function decrypt($data, $key) {
        return base64_decode($data);
    }
}
