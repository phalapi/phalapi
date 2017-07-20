<?php
namespace PhalApi\Tests;

use PhalApi\Cache\APCUCache;
use PhalApi\Exception\InternalServerErrorException;

include_once dirname(__FILE__) . '/apcu.php';

/**
 * PhpUnderControl_PhalApi_Cache_APCU_Test
 *
 * 针对 ./PhalApi_Cache_APCU.php PhalApi_Cache_APCU 类的PHPUnit单元测试
 *
 * @author: dogstar 20170413
 */

class PhpUnderControl_PhalApi_Cache_APCU_Test extends \PHPUnit_Framework_TestCase
{
    public $aPCU;

    protected function setUp()
    {
        parent::setUp();

        $this->aPCU = new APCUCacheMock();

        $this->aPCU->delete('apcu_test_key');
    }

    protected function tearDown()
    {
    }


    /**
     * @group testSet
     */ 
    public function testSet()
    {
        $key = 'apcu_test_key';
        $value = '2017';
        $expire = 60;

        $rs = $this->aPCU->set($key, $value, $expire);
    }

    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'apcu_test_key';

        $this->aPCU->set($key, '2017', 10);
        $rs = $this->aPCU->get($key);

        $this->assertEquals('2017', $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $key = 'apcu_test_key';

        $rs = $this->aPCU->delete($key);
        $this->assertTrue($rs);

        $this->assertSame(NULL, $this->aPCU->get($key));
    }

    public function testSetAndSetAgain()
    {
        $key = 'apcu_test_key_again';

        $this->aPCU->set($key, 'A1', 60);
        $this->aPCU->set($key, 'A2', 60);
        $this->aPCU->set($key, 'A3', 60);

        $this->assertEquals('A3', $this->aPCU->get($key));
    }

    public function testWithoutAPCU()
    {
        try {
            $apcu = new APCUCache();
        } catch (InternalServerErrorException $ex) {
        }
    }
}

class APCUCacheMock extends APCUCache {
    public function __construct() {
    }
}
