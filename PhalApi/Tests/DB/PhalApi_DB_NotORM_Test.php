<?php
/**
 * PhpUnderControl_PhalApiDBNotORM_Test
 *
 * 针对 ../../PhalApi/DB/NotORM.php PhalApi_DB_NotORM 类的PHPUnit单元测试
 *
 * @author: dogstar 20141122
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_DB_NotORM')) {
    require dirname(__FILE__) . '/../../PhalApi/DB/NotORM.php';
}

$_GET['__sql__'] = 1;

class PhpUnderControl_PhalApiDBNotORM_Test extends PHPUnit_Framework_TestCase
{
    public $notorm;

    protected function setUp()
    {
        parent::setUp();

        $this->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'));
        $this->notorm->debug = true;
    }

    protected function tearDown()
    {
    }

    /**
     * @dataProvider provideTable
     */
    public function testHere($table)
    {
        $demo = $this->notorm->$table;
        $this->assertNotNull($demo);
        //var_dump($demo);

        $rs = $demo->fetchAll();
        //var_dump($rs);
        $this->assertNotEmpty($rs);
    }

    public function provideTable()
    {
        return array(
            array('demo'),
            array('demo_0'),
            array('demo_1'),
            array('demo_3'),
        );
    }

    /**
     * @expectedException PhalApi_Exception
     */
    public function testNoMap()
    {
        $notorm = new PhalApi_DB_NotORM(array());
        $rs = $notorm->demo->fetchAll();
    }

    public function testNoDbRouter()
    {
        $rs = $this->notorm->demo->fetchAll();
        $this->assertNotEmpty($rs);
    }

    public function testUseDefaultDbKey()
    {
        $rs = $this->notorm->demo_10->fetchAll();
        $this->assertNotEmpty($rs);
    }

    public function testMultiSet()
    {
        $this->notorm->debug = true;
        $this->notorm->debug = false;
    }

    public function testTransactionCommit()
    {
        //Step 1: 开启事务
        $this->notorm->beginTransaction('DB_A');

        //Step 2: 数据库操作
        $this->notorm->demo->insert(array('name' => 'commit at ' . $_SERVER['REQUEST_TIME']));
        $this->notorm->demo->insert(array('name' => 'commit again at ' . $_SERVER['REQUEST_TIME']));

        //Step 3: 提交事务
        $this->notorm->commit('DB_A');

    }

    public function testTransactionRollback()
    {
        //Step 1: 开启事务
        $this->notorm->beginTransaction('DB_A');

        //Step 2: 数据库操作
        $this->notorm->demo->insert(array('name' => 'test rollback'));

        //Step 3: 回滚事务
        $this->notorm->rollback('DB_A');

        $rs = $this->notorm->demo->where('name', 'test rollback')->fetchRow();
        $this->assertEmpty($rs);
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testTransactionException()
    {
        $this->notorm->beginTransaction('NO_THIS_DB');
    }

    public function testFetchPairs()
    {

        $rs = $this->notorm->demo->fetchPairs('id', 'name');
        //var_dump($rs);
        foreach ($rs as $key => $row) {
            $this->assertTrue(is_string($row));
        }

        $rs = $this->notorm->demo->select('name')->fetchPairs('id');
        //var_dump($rs);
        foreach ($rs as $key => $row) {
            $this->assertTrue(is_array($row));
            $this->assertArrayHasKey('name', $row);
        }
    }

    public function testLimitInQueryRows()
    {
        //int
        $sql = 'SELECT * FROM demo LIMIT :start, :len';
        $params = array(':start' => 1, ':len' => 2);
        $rows = $this->notorm->demo->queryRows($sql, $params);
        var_dump($rows);

        //string
        $params = array(':start' => '1', ':len' => '2');
        $rows = $this->notorm->demo->queryRows($sql, $params);
        var_dump($rows);

        //int and string
        $params = array(':start' => '1', ':len' => 2);
        $rows = $this->notorm->demo->queryRows($sql, $params);
        var_dump($rows);
    }
}
