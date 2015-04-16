#!/usr/bin/env php
<?php
/**
 * PhpUnderControl_PhalApiCookie_Test
 *
 * 针对 ../../../PhalApi/PhalApi/Cookie.php PhalApi_Cookie 类的PHPUnit单元测试
 *
 * @author: dogstar 20150411
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Cookie')) {
    require dirname(__FILE__) . '/../../../PhalApi/PhalApi/Cookie.php';
}

class PhpUnderControl_PhalApiCookie_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCookie;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiCookie = new PhalApi_Cookie();
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

        $rs = $this->phalApiCookie->get($key);

        $this->assertTrue(is_array($rs));

        $this->assertNull($this->phalApiCookie->get('noThisKey'));

        $_COOKIE['aKey'] = 'phalapi';
        $key = 'aKey';
        $this->assertEquals('phalapi', $this->phalApiCookie->get($key));
    }

    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'bKey';
        $value = '2015';

        $rs = @$this->phalApiCookie->set($key, $value);

        //should not get in this time, but next time
        $this->assertNull($this->phalApiCookie->get($key));
    }

}
