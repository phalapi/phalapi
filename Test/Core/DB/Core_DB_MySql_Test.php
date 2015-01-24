<?php
/**
 * PhpUnderControl_CoreDBMySql_Test
 *
 * 针对 ../../../Core/DB/MySql.php Core_DB_MySql 类的PHPUnit单元测试
 *
 * @author: dogstar 20141019
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('Core_DB_MySql')) {
    require dirname(__FILE__) . '/../../../Core/DB/MySql.php';
}

class PhpUnderControl_CoreDBMySql_Test extends PHPUnit_Framework_TestCase
{
    public $coreDBMySql;

    public static function setUpBeforeClass()
    {
        $coreDBMySql = new Core_DB_MySql(Core_DI::one()->config->get('sys.db'));

        $coreDBMySql->connect();

        $coreDBMySql->debug();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->coreDBMySql = new Core_DB_MySql(Core_DI::one()->config->get('sys.db'));
    }

    protected function tearDown()
    {
    }


    /**
     * @group testDisconnect
     */ 
    public function testDisconnect()
    {
        $rs = $this->coreDBMySql->disconnect();
    }

    /**
     * @group testGetTableName
     */ 
    public function testGetTableName()
    {
        $tableName = '';

        $rs = $this->coreDBMySql->getTableName($tableName);
    }

    /**
     * @group testExec
     */ 
    public function testExec()
    {
        $sql = 'UPDATE weili_user set nickname = "aHa~" where username = ?';
        $bindings = array ('phpunit');

        $rs = $this->coreDBMySql->exec($sql, $bindings);
    }

    public function testExecWithQuery()
    {
        $sql = 'select * from weili_user';

        $rs = $this->coreDBMySql->exec($sql);
    }

    /**
     * @group testCount
     */ 
    public function testCount()
    {
        $tableName = 'user';
        $addSQL = 'username = ?';
        $bindings = array ('phpunit');

        $rs = $this->coreDBMySql->count($tableName, $addSQL, $bindings);

        $this->assertEquals(1, $rs);
    }

    /**
     * @group testGetRow
     */ 
    public function testGetRow()
    {
        $rs = $this->coreDBMySql->getRow('user', 'id, salt, regtime', 'username = ?', array('phpunit'));

        $this->assertTrue(is_array($rs));
        $this->assertArrayHasKey('id', $rs);
        $this->assertArrayHasKey('salt', $rs);
        $this->assertArrayHasKey('regtime', $rs);
    }

    /**
     * @group testGetRow
     */
    public function testGetRowButEmpty()
    {
        $rs = $this->coreDBMySql->getRow('user', 'id, salt, regtime', 'username = ?', array('phpunit_no_thisUserName'));

        $this->assertTrue(is_array($rs));
        $this->assertEmpty($rs);
    }

    /**
     * @group testGetAll
     */ 
    public function testGetAll()
    {
        $rs = $this->coreDBMySql->getAll('user', 'nickname, salt, id', 'regtime > ?', array(0), 'id desc', 0, 10);

        $this->assertTrue(is_array($rs));
        $this->assertGreaterThan(0, count($rs));
        foreach ($rs as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('nickname', $item);
            $this->assertArrayHasKey('salt', $item);
        }
    }

    /**
     * @group testAdd
     */ 
    public function testAdd()
    {
        $username = 'phpunit_testAdd';

        //delete
        $this->coreDBMySql->exec('delete from weili_user where username = ?', array($username));

        //add
        $tableName = 'user';
        $data = array('username' => $username, 'nickname' => 'aHa~', 'password' => '112233', 'salt' => 'abc');

        $rs = $this->coreDBMySql->add($tableName, $data);

        $this->assertGreaterThan(0, $rs);
    }

    /**
     * @group testAdd
     */ 
    public function testAddWithEmpty()
    {
        $rs = $this->coreDBMySql->add('user', array());
        $this->assertEquals(0, $rs);
    }

    /**
     * @group testUpdate
     */ 
    public function testUpdate()
    {
        $data = array('nickname' => 'AHA!!!', 'regtime' => time());

        $rs = $this->coreDBMySql->update('user', $data, 'username = "phpunit_testAdd"');

        $this->assertEquals(1, $rs);
    }

    /**
     * @group testUpdate
     */ 
    public function testUdpateWithEmpty()
    {
        $data = array();
        $rs = $this->coreDBMySql->update('user', $data, 'username = "phpunit_testAdd"');
        $this->assertEquals(0, $rs);
    }

    /**
     * @group testUpdate
     */ 
    public function testUdpateNoThisRecored()
    {
        $data = array('nickname' => 'AHA!!!', 'regtime' => time());
        $rs = $this->coreDBMySql->update('user', $data, 'username = "phpunit_testAdd_noThisRecord"');
        $this->assertEquals(0, $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $username = 'phpunit_testDelete';

        //add
        $tableName = 'user';
        $data = array('username' => $username, 'nickname' => 'aHa~', 'password' => '112233', 'salt' => 'abc');

        $rs = $this->coreDBMySql->add($tableName, $data);


        $query = array('username' => $username);

        $rs = $this->coreDBMySql->delete($tableName, $query);
    }

    /**
     * @group testTestConnection
     */ 
    public function testTestConnection()
    {
        $rs = $this->coreDBMySql->testConnection();
    }

    /**
     * @group testDump
     */ 
    public function testDump()
    {
        $data = '';

        $rs = $this->coreDBMySql->dump($data);
    }

}
