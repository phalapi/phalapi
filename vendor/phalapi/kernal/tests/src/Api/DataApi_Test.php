<?php
/**
 * PhalApi_PhalApi\Api\DataApi_Test
 *
 * 针对 ../src/Api/DataApi.php PhalApi\Api\DataApi 类的PHPUnit单元测试
 *
 * @author: dogstar 20200313
 */

namespace tests\PhalApi\Api;
use PhalApi\Api\DataApi;


class DemoInnerDataModelForDataApi extends \PhalApi\Model\DataModel {
    public function getTableName($id) {
        return 'demo';
    }
}

class DataApiInnerForTest extends \PhalApi\Api\DataApi {

    protected function getDataModel() {
        return new DemoInnerDataModelForDataApi();
    }
}

class PhpUnderControl_PhalApiApiDataApi_Test extends \PHPUnit\Framework\TestCase
{
    public $phalApiApiDataApi;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiApiDataApi = new DataApiInnerForTest();
        //\PhalApi\Api\DataApi();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }


    /**
     * @group testGetRules
     */ 
    public function testGetRules()
    {
        $rs = $this->phalApiApiDataApi->getRules();
        $this->assertTrue(is_array($rs));
    }

    /**
     * @group testTableList
     */ 
    public function testTableList()
    {
        $this->phalApiApiDataApi->searchParams = array();
        $this->phalApiApiDataApi->page = 1;
        $this->phalApiApiDataApi->limit = 2;
        $rs = $this->phalApiApiDataApi->tableList();
        // var_dump($rs);

        $this->assertGreaterThan(0, $rs['total']);
        $this->assertNotEmpty($rs['items']);
    }

    /**
     * @group testCreateData
     */ 
    public function testCreateData()
    {
        $this->phalApiApiDataApi->newData = array('name' => 'dogstar api');
        $rs = $this->phalApiApiDataApi->createData();

        $this->assertGreaterThan(0, $rs['id']);

        return $rs['id'];
    }

    /**
     * @group testDeleteDataIDs
     * @depends testCreateData
     */ 
    public function testDeleteDataIDs($id)
    {
        $this->phalApiApiDataApi->ids = array($id);
        $rs = $this->phalApiApiDataApi->deleteDataIDs();
        $this->assertEquals(1, $rs['deleted_num']);
    }

    /**
     * @group testGetData
     */ 
    public function testGetData()
    {
        $this->phalApiApiDataApi->id = 3;
        $rs = $this->phalApiApiDataApi->getData();
        $this->assertNotEmpty($rs);
    }

    /**
     * @group testUpdateData
     */ 
    public function testUpdateData()
    {
        $this->phalApiApiDataApi->newData = array('name' => 'dogstar api');
        $rs = $this->phalApiApiDataApi->createData();

        $this->phalApiApiDataApi->id = $rs['id'];
        $this->phalApiApiDataApi->data = array('name' => time()); 
        $rs = $this->phalApiApiDataApi->updateData();

        $this->assertEquals(1, $rs['updated_num']);
    }

    public function testDeleteData() {
        $this->phalApiApiDataApi->id = 1000004;

        $rs = $this->phalApiApiDataApi->deleteData();

        $this->assertEquals(0, $rs['deleted_num']);
    }

}
