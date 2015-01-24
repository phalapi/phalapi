<?php
/**
 * PhpUnderControl_CoreDBNotORM_Test
 *
 * 针对 ../../../Core/DB/NotORM.php Core_DB_NotORM 类的PHPUnit单元测试
 *
 * @author: dogstar 20141122
 */

require_once dirname(__FILE__) . '/../../test_env.php';

if (!class_exists('Core_DB_NotORM')) {
    require dirname(__FILE__) . '/../../../Core/DB/NotORM.php';
}

$_GET['__sql__'] = 1;

class PhpUnderControl_CoreDBNotORM_Test extends PHPUnit_Framework_TestCase
{
    public $coreDBNotORM;

    protected function setUp()
    {
        parent::setUp();

        $config = array(
            /**
             * avaiable db servers
             */
            'servers' => array(
                'db_138' => array(
                    'host'      => '120.24.60.138',         //数据库域名
                    'name'      => 'dogstar_test',                 //数据库名字
                    'user'      => 'apps',                  //数据库用户名
                    'password'  => '^m2G4H&Xz',             //数据库密码
                    'port'      => '3306',                  //数据库端口
                ),
                'db_demo' => array(
                    'host'      => '120.24.60.130',         //数据库域名
                    'name'      => 'test',                 //数据库名字
                    'user'      => 'apps',                  //数据库用户名
                    'password'  => 'wetime',                //数据库密码
                    'port'      => '3306',                  //数据库端口
                ),
            ),

            /**
             * custom table map
             */
            'tables' => array(
                '__default__' => array(
                    'prefix' => 'weili_',
                    'key' => 'id',
                    'map' => array(
                        array('db' => 'db_138'),
                    ),
                ),
                'demo' => array(
                    'prefix' => 'weili_',
                    'key' => 'id',
                    'map' => array(
                        array('db' => 'db_138'),
                        array('start' => 0, 'end' => 2, 'db' => 'db_138'),
                        array('start' => 3, 'end' => 5, 'db' => 'db_demo'),
                    ),
                ),
            ),
        );

        $this->coreDBNotORM = new Core_DB_NotORM($config);
        $this->coreDBNotORM->debug = true;
    }

    protected function tearDown()
    {
    }

    /**
     * @dataProvider provideTable
     */
    public function testHere($table)
    {
        $demo = $this->coreDBNotORM->$table;
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
     * @expectedException Core_Exception
     */
    public function testNoMap()
    {
        $notorm = new Core_DB_NotORM(array());
        $rs = $notorm->demo->fetchAll();
    }

    public function testNoDbRouter()
    {
        $rs = $this->coreDBNotORM->demo->fetchAll();
        $this->assertNotEmpty($rs);
    }

    public function testUseDefaultDbKey()
    {
        $rs = $this->coreDBNotORM->demo_10->fetchAll();
        $this->assertNotEmpty($rs);
    }
}
