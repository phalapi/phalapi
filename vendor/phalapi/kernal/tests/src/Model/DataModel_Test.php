<?php
/**
 * PhalApi_PhalApi\Model\DataModel_Test
 *
 * 针对 ./src/Model/DataModel.php PhalApi\Model\DataModel 类的PHPUnit单元测试
 *
 * @author: dogstar 20200308
 */

namespace tests\PhalApi\Model;
use PhalApi\Model\DataModel;

class DemoInnerDataModel extends \PhalApi\Model\DataModel {
    public function getTableName($id) {
        return 'demo';
    }
}

class PhpUnderControl_PhalApiModelDataModel_Test extends \PHPUnit\Framework\TestCase
{
    public $phalApiModelDataModel;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiModelDataModel = new DemoInnerDataModel(); // \PhalApi\Model\DataModel();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }


    /**
     * @group testCount
     */ 
    public function testCount()
    {
        $rs = $this->phalApiModelDataModel->count();

        $this->assertTrue(is_int($rs));
        $this->assertGreaterThan(0, $rs);

    }

    public function testCountByWhere()
    {
        $rs = $this->phalApiModelDataModel->count('id > 1 AND id < 10');

        $this->assertTrue(is_int($rs));
        $this->assertGreaterThan(0, $rs);

    }

    public function testCountByWhereMore()
    {
        $rs = $this->phalApiModelDataModel->count('id > 1 AND id < 10', 'id');

        $this->assertTrue(is_int($rs));
        $this->assertGreaterThan(0, $rs);

    }

    public function testMinByWhere()
    {
        $rs = $this->phalApiModelDataModel->min('id > 1 AND id < 10', 'id');

        $this->assertTrue(is_int($rs));
        $this->assertGreaterThan(0, $rs);

    }

    public function testMaxByWhere()
    {
        $rs = $this->phalApiModelDataModel->max('id > 1 AND id < 10', 'id');

        $this->assertTrue(is_int($rs));
        $this->assertGreaterThan(0, $rs);

    }

    /**
     * @group testSum
     */ 
    public function testSum()
    {
        $where = 'id > 1 AND id < 10';
        $sumBy = 'id';

        $rs = $this->phalApiModelDataModel->sum($where, $sumBy);

        $this->assertGreaterThan(0, $rs);
    }

    public function testSumMore()
    {
        $where = array('id > ?' => 1, 'id < ?' => 10);
        $sumBy = 'id';

        $rs = $this->phalApiModelDataModel->sum($where, $sumBy);

        $this->assertGreaterThan(0, $rs);
    }

    /**
     * @group testGetValueBy
     */ 
    public function testGetValueBy()
    {
        $field = 'id';
        $value = 1;
        $selectFiled = 'name';
        $default = false;

        $rs = $this->phalApiModelDataModel->getValueBy($field, $value, $selectFiled, $default);

        $this->assertEquals(1, $rs);
    }

    /**
     * @group testGetValueMoreBy
     */ 
    public function testGetValueMoreBy()
    {
        $field = 'name';
        $value = 'dogstar';
        $selectFiled = 'id';
        $limit = 10;
        $isDistinct = false;

        $rs = $this->phalApiModelDataModel->getValueMoreBy($field, $value, $selectFiled, $limit, $isDistinct);
        // var_dump($rs);

        $this->assertNotEmpty($rs);
    }

    public function testGetValueByCall()
    {
        $this->assertTrue(true);
        return;

        $field = 'id';
        $value = 1;
        $selectFiled = 'name';
        $default = false;

        $rs = $this->phalApiModelDataModel->getValueById($value, $selectFiled, $default);

        $this->assertEquals(1, $rs);
    }


    public function testGetValueMoreByCall()
    {
        $this->assertTrue(true);
        return;

        $field = 'name';
        $value = 'dogstar';
        $selectFiled = 'id';
        $limit = 10;
        $isDistinct = true;

        $rs = $this->phalApiModelDataModel->getValueMoreByName($value, $selectFiled, $limit, $isDistinct);
        // var_dump($rs);

        $this->assertNotEmpty($rs);
    }

    /**
     * @group testGetDataBy
     */ 
    public function testGetDataBy()
    {
        $field = 'id';
        $value = '1';
        $select = '*';
        $default = false;

        $rs = $this->phalApiModelDataModel->getDataBy($field, $value);
        // var_dump($rs);
        $this->assertArrayHasKey('name', $rs);

        $rs = $this->phalApiModelDataModel->getDataBy($field, $value, 'name');
        // var_dump($rs);
        $this->assertArrayHasKey('name', $rs);

        $rs = $this->phalApiModelDataModel->getDataBy('name', 'xxxxbbbbbb', 'name', array('name' => 'hhh'));
        $this->assertEquals('hhh', $rs['name']);

        $rs = $this->phalApiModelDataModel->getDataByName($value, 'name');
        // var_dump($rs);
        $this->assertArrayHasKey('name', $rs);
    }

    /**
     * @group testGetDataMoreBy
     */ 
    public function testGetDataMoreBy()
    {
        $field = 'name';
        $value = 'dogstar';
        $limit = 10;
        $select = '*';

        $rs = $this->phalApiModelDataModel->getDataMoreBy($field, $value, $limit, $select);

        foreach ($rs as $it) {
            $this->assertArrayHasKey('name', $it);
        }

        $this->assertNotEmpty($rs);
    }

    /**
     * @group testGetData
     */ 
    public function testGetData()
    {
        $where = 'id = 1';
        $select = '*';
        $default = false;

        $rs = $this->phalApiModelDataModel->getData($where, $select, $default);

        $this->assertNotEmpty($rs);

        // 静态方法
        $this->assertNotEmpty(DemoInnerDataModel::model()->getData($where, $select, $default));
    }

    /**
     * @group testGetList
     */ 
    public function testGetList()
    {
        $where = 'id > :sid AND id < :eid';
        $whereParams = array (
            ':sid' => 1, ':eid' => 10
);
        $select = '*';
        $order = 'id DESC';
        $page = 1;
        $perpage = 3;

        $rs = $this->phalApiModelDataModel->getList($where, $whereParams, $select, $order, $page, $perpage);
        // var_dump($rs);

        foreach ($rs as $it) {
            $this->assertArrayHasKey('name', $it);
        }
    }

    /**
     * @group testDeleteAll
     */ 
    public function testDeleteAll()
    {
        $where = 'id = 999';

        $rs = $this->phalApiModelDataModel->deleteAll($where);

        $this->assertEquals(0, $rs);
    }

    /**
     * @group testDeleteIds
     */ 
    public function testDeleteIds()
    {
        $ids = array(999999, 888888, 77777);

        $rs = $this->phalApiModelDataModel->deleteIds($ids);
    }

    /**
     * @group testUpdateAll
     */ 
    public function testUpdateAll()
    {
        $where = 'id = 5';
        $updateData = array('name' => time());

        $rs = $this->phalApiModelDataModel->updateAll($where,$updateData);

        $this->assertEquals(1, $rs);
    }

    /**
     * @group testUpdateCounter
     */ 
    public function testUpdateCounter()
    {
        $where = 'name = "dogstar"';
        $updateData = array('age' => 1);

        $rs = $this->phalApiModelDataModel->updateCounter($where, $updateData);

        $this->assertTrue(true);
    }

    /**
     * @group testInsertMore
     */ 
    public function testInsertMore()
    {
        $datas = array(array('name' => time()), array('name' => 'dogstar'));
        $isIgnore = false;

        $rs = $this->phalApiModelDataModel->insertMore($datas, $isIgnore);

        $this->assertEquals(2, $rs);
    }

    public function testModel() {
        $rs = DemoInnerDataModel::model();
        $this->assertInstanceOf('\tests\PhalApi\Model\DemoInnerDataModel', $rs);
        //var_dump($rs);
    }

    public function testNOtORM() {
        $rs = DemoInnerDataModel::notorm();
        // var_dump($rs);
        $this->assertInstanceOf('\NotORM_Result', $rs);
    }

    public function testQueryAll() {
        $sql = 'select * from tbl_demo where id < ?';
        $rs = $this->phalApiModelDataModel->queryAll($sql, array(10));
        // var_dump($rs);
        $this->assertNotEmpty($rs);
    }

    public function testExecuteSql() {
        $sql = 'update tbl_demo set age = age + 1 where id = :id';
        $params = array(':id' => 776);
        $rs = $this->phalApiModelDataModel->executeSql($sql, $params);
        // var_dump($rs);
        $this->assertEquals(0, $rs);
    }

}
