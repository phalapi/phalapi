<?php
namespace PhalApi\Tests;

use PhalApi\Model\Query;
use PhalApi\Model\Proxy;

/**
 * PhpUnderControl_PhalApiModelProxy_Test
 *
 * 针对 ../PhalApi/ModelProxy.php PhalApi_ModelProxy 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

class PhpUnderControl_PhalApiModelProxy_Test extends \PHPUnit_Framework_TestCase
{
    public $proxy;

    protected function setUp()
    {
        parent::setUp();

        $this->proxy = new ProxyMock();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGetData
     */ 
    public function testGetData()
    {
        $query = new Query();
        $query->id = 1;

        $rs = $this->proxy->getData($query);
    }

    public function testGetDataWithNoCache()
    {
        $query = new Query();
        $query->id = 1;
        $query->readCache = false;
        $query->writeCache = false;

        $rs = $this->proxy->getData($query);
        $this->assertEquals('heavy data', $rs);
    }

    public function testGetDataWithCache()
    {
        $query = new Query();
        $query->id = 1;
        $query->readCache = true;
        $query->writeCache = true;

        $rs = $this->proxy->getData($query);
        $this->assertEquals('heavy data', $rs);
    }

    public function testNewWithNull()
    {
        $proxy = new ProxyMock(NULL);
        $proxy->getData();
    }
}

class ProxyMock extends Proxy {

    protected function doGetData($query) {
        return 'heavy data';
    }

    protected function getKey($query) {
        return 'heavy_data_' . $query->id;
    }

    protected function getExpire($query) {
        return 10;
    }
}
