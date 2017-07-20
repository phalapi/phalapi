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
}
