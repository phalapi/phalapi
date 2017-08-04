<?php
namespace PhalApi\Tests;

use PhalApi\Cache\NoneCache;

/**
 * PhpUnderControl_PhalApiCacheNone_Test
 *
 * 针对 ../../PhalApi/Cache/None.php PhalApi_Cache_None 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

class PhpUnderControl_PhalApiCacheNone_Test extends \PHPUnit_Framework_TestCase
{
    public $noneCache;

    protected function setUp()
    {
        parent::setUp();

        $this->noneCache = new NoneCache();
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

        $rs = $this->noneCache->set($key, $value, $expire);
    }

    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'aKey';

        $rs = $this->noneCache->get($key);

        $this->assertNull($rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'aKey';

        $rs = $this->noneCache->delete($key);
    }

}
