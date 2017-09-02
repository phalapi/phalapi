<?php
/**
 * PhpUnderControl_PhalApiModelProxy_Test
 *
 * 针对 ../PhalApi/ModelProxy.php PhalApi_ModelProxy 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_ModelProxy')) {
    require dirname(__FILE__) . '/../PhalApi/ModelProxy.php';
}

class PhpUnderControl_PhalApiModelProxy_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiModelProxy;

    protected $diCache;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiModelProxy = new PhalApi_ModelProxy_Mock();

        $this->diCache = DI()->cache;
    }

    protected function tearDown()
    {
        DI()->cache = $this->diCache;
    }


    /**
     * @group testGetData
     */ 
    public function testGetData()
    {
        $query = new PhalApi_ModelQuery();
        $query->id = 1;

        $rs = $this->phalApiModelProxy->getData($query);
    }

    public function testGetDataWithNoCache()
    {
        $query = new PhalApi_ModelQuery();
        $query->id = 1;
        $query->readCache = false;
        $query->writeCache = false;

        $rs = $this->phalApiModelProxy->getData($query);
    }

    public function testGetDataWithCache()
    {
        $query = new PhalApi_ModelQuery();
        $query->id = 1;
        $query->readCache = true;
        $query->writeCache = true;

        $rs = $this->phalApiModelProxy->getData($query);
        $this->assertEquals('heavy data', $rs);

        $rs = $this->phalApiModelProxy->getData($query);
        $this->assertEquals('heavy data', $rs);
    }

    public function testNewWithNull()
    {
        DI()->cache = NULL;
        $proxy = new PhalApi_ModelProxy_Mock(NULL);
        $proxy->getData();
    }

}

class PhalApi_ModelProxy_Mock extends PhalApi_ModelProxy {

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
