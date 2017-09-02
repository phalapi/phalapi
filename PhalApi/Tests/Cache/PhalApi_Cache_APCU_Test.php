<?php
/**
 * PhpUnderControl_PhalApi_Cache_APCU_Test
 *
 * 针对 ./PhalApi_Cache_APCU.php PhalApi_Cache_APCU 类的PHPUnit单元测试
 *
 * @author: dogstar 20170413
 */

require_once dirname(__FILE__) . '/../test_env.php';
include_once dirname(__FILE__) . '/apcu.php';

if (!class_exists('PhalApi_Cache_APCU')) {
}

class PhpUnderControl_PhalApi_Cache_APCU_Test extends PHPUnit_Framework_TestCase
{
    public $aPCU;

    protected function setUp()
    {
        parent::setUp();

        $this->aPCU = new PhalApi_Cache_APCU_Mock();

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

        $rs = $this->aPCU->get($key);

        //$this->assertEquals('2017', $rs);
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
            $apcu = new PhalApi_Cache_APCU();
        } catch (PhalApi_Exception_InternalServerError $ex) {
            echo "Aye~";
        }
    }


}

class PhalApi_Cache_APCU_Mock extends PhalApi_Cache_APCU {

    public function __construct() {
    }
}
