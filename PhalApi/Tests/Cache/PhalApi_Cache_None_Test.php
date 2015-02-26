<?php
/**
 * PhpUnderControl_PhalApiCacheNone_Test
 *
 * 针对 ../../PhalApi/Cache/None.php PhalApi_Cache_None 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Cache_None')) {
    require dirname(__FILE__) . '/../../PhalApi/Cache/None.php';
}

class PhpUnderControl_PhalApiCacheNone_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiCacheNone;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiCacheNone = new PhalApi_Cache_None();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'aKey';
        $value = 'aValue';
        $expire = '100';

        $rs = $this->phalApiCacheNone->set($key, $value, $expire);
    }

    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'aKey';

        $rs = $this->phalApiCacheNone->get($key);

        $this->assertNull($rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'aKey';

        $rs = $this->phalApiCacheNone->delete($key);
    }

}
