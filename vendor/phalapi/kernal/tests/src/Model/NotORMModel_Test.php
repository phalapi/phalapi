<?php
namespace PhalApi\Tests;

use PhalApi\Model\NotORMModel;

/**
 * PhpUnderControl_PhalApiModelNotORM_Test
 *
 * 针对 ../../PhalApi/Model/NotORM.php PhalApi_Model_NotORM 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

class NotORMTest extends NotORMModel {
    public function getTableName($id) {
        return parent::getTableName($id);
    }
}

class NotORMTestModel extends NotORMModel {
    public function getTableName($id) {
        return 'notormtest';
    }
}

include_once dirname(__FILE__) . '/app.php';
include_once dirname(__FILE__) . '/app_fun.php';
include_once dirname(__FILE__) . '/fun.php';

class PhpUnderControl_PhalApiModelNotORM_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiModelNotORM;

    protected function setUp()
    {
        parent::setUp();
        $this->phalApiModelNotORM = new NotORMTestModel();
    }

    protected function tearDown()
    {
        // var_dump(\PhalApi\DI()->tracer->getSqls());
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $id = '1';
        $fields = '*';

        $rs = $this->phalApiModelNotORM->get($id, $fields);

        $this->assertNotEmpty($rs);

        $this->assertEquals('welcome here', $rs['content']);
    }

    /**
     * @group testInsert
     */ 
    public function testInsert()
    {
        $data = array('id' => 100, 'content' => 'phpunit', 'ext_data' => array('year' => 2015));
        $id = NULL;

        $rs = $this->phalApiModelNotORM->insert($data, $id);

        $rs = $this->phalApiModelNotORM->get(100, 'content, ext_data');

        $this->assertEquals('phpunit', $rs['content']);
        $this->assertEquals(array('year' => 2015), $rs['ext_data']);
    }

    public function testMultiInsert()
    {
        $data = array(
            array('content' => 'phpunit_insert_1', 'ext_data' => array('year' => 2015)),
            array('content' => 'phpunit_insert_2', 'ext_data' => array('year' => 2018))
        );

        $rs = \PhalApi\DI()->notorm->notormtest->insert_multi($data);

        // 插入成功，返回的条目数量
        $this->assertEquals(2, $rs);
    }

    /**
     * PhalApi 2.5.0 新特性
     */
    public function testMultiInsertAndIgnore()
    {
        $data = array(
            array('content' => 'phpunit_insert_ignore_1', 'ext_data' => array('year' => 2015)),
            array('content' => 'phpunit_insert_ignore_2', 'ext_data' => array('year' => 2018))
        );
        $isIgnore = true;

        $rs = \PhalApi\DI()->notorm->notormtest->insert_multi($data, $isIgnore);

        // 插入成功，返回的条目数量
        $this->assertEquals(2, $rs);
    }

    /**
     * @group testUpdate
     * @depends testInsert
     */ 
    public function testUpdate()
    {
        $id = '100';
        $data = array('content' => 'phpunit2', 'ext_data' => array('year' => 2020));

        $this->phalApiModelNotORM->update($id, $data);

        $rs = $this->phalApiModelNotORM->get($id, 'content, ext_data');

        $this->assertEquals('phpunit2', $rs['content']);
        $this->assertEquals(array('year' => 2020), $rs['ext_data']);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $id = '100';

        $rs = $this->phalApiModelNotORM->delete($id);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideDefaultTableData
     */
    public function testDefaultTable($tableName, $tableClass)
    {
        $model = new $tableClass();
        $this->assertEquals($tableName, $model->getTableName(null));
    }

    public function provideDefaultTableData()
    {
        return array(
            array('tmp2', '\\App\\Model\\Tmp'),
            array('test', '\\App\\Model\\Test'),
            array('defaulttbl', '\\App\\Model\\DefaultTbl'),
            array('defaulttbl', '\\Fun\\Model\\DefaultTbl'),
            array('demo', '\\App\\Fun\\Model\\Demo'),
            array('userfriends', '\\App\\Model\\UserFriends'),
            array('user_message', '\\App\\Fun\\Model\\User\\Message'),
        );
    }

    /**
     * 执行带结果的原生sql，只要用于插入、更新、删除等
     */
    public function testExcuteSql()
    {
        // 原生插入
        $sql = "INSERT  INTO tbl_notormtest (`content`, `ext_data`) VALUES ('phpunit_e_sql_1', '" . '{\"year\":2019}' . "');";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql);
        $this->assertEquals(1, $rs);

        // 原生绑定参数插入
        $sql = "INSERT  INTO tbl_notormtest (`content`, `ext_data`) VALUES (:content, :ext_data);";
        $params = array(':content' => 'phpunit_e_sql_2', ':ext_data' => '{\"year\":2020}');
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);
        $this->assertEquals(1, $rs);

        // 原生更新
        $sql = "UPDATE tbl_notormtest SET `content` = 'phpunit_e_sql_3' WHERE (content = ? OR content = ?);";
        $params = array('phpunit_e_sql_1', 'phpunit_e_sql_2');
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);
        $this->assertEquals(2, $rs);

        // 如果是查询呢？只会返回影响的行数，而非结果
        $sql = "SELECT * FROM tbl_notormtest WHERE content IN ('phpunit_e_sql_3')";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql, $params);
        $this->assertEquals(2, $rs);

        // 原生删除
        $sql = "DELETE FROM tbl_notormtest WHERE (content IN ('phpunit_e_sql_1', 'phpunit_e_sql_2'));";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql);
        $this->assertEquals(0, $rs);

        $sql = "DELETE FROM tbl_notormtest WHERE (content IN ('phpunit_e_sql_3'));";
        $rs = \PhalApi\DI()->notorm->notormtest->executeSql($sql);
        $this->assertEquals(2, $rs);
    }

    public function testUpdateCounter()
    {
        $oldData = \PhalApi\DI()->notorm->notormtest->where('id', 1)->fetchPairs('id', 'year');

        $rs = \PhalApi\DI()->notorm->notormtest->where('id', 1)->updateCounter('year', 1);
        $afterIncdData = \PhalApi\DI()->notorm->notormtest->where('id', 1)->fetchPairs('id', 'year');
        $this->assertEquals($afterIncdData[1], $oldData[1] + 1);
        $this->assertEquals(1, $rs);


        $rs = \PhalApi\DI()->notorm->notormtest->where('id', 1)->updateCounter('year', -1);
        $afterDecData = \PhalApi\DI()->notorm->notormtest->where('id', 1)->fetchPairs('id', 'year');
        $this->assertEquals($afterDecData[1], $afterIncdData[1] - 1);
        $this->assertEquals(1, $rs);
    }

    public function testUpdateMultiCounters()
    {
        $oldData = \PhalApi\DI()->notorm->notormtest->where('id', 1)->fetchPairs('id', 'year');

        $rs = \PhalApi\DI()->notorm->notormtest->where('id', 1)->updateMultiCounters(array('year' => 2));
        $this->assertEquals(1, $rs);

        $rs = \PhalApi\DI()->notorm->notormtest->where('id', 1)->updateMultiCounters(array('year' => -2));
        $this->assertEquals(1, $rs);

        $newData = \PhalApi\DI()->notorm->notormtest->where('id', 1)->fetchPairs('id', 'year');

        $this->assertEquals($newData[1], $oldData[1]);
    }
}
