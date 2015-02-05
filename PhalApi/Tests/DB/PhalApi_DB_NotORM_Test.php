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
    public $coreDBNotORM;

    protected function setUp()
    {
        parent::setUp();

        $config = array(
            /**
             * avaiable db servers
             */
            'servers' => array(
                'DB_A' => array(
                    'host'      => '192.168.37.190',           //数据库域名
                    'name'      => 'phalapi_test',                  //数据库名字
                    'user'      => 'root',                  //数据库用户名
                    'password'  => '123456',                //数据库密码
                    'port'      => '3306',                  //数据库端口
                ),
                'DB_DEMO' => array(
                    'host'      => '192.168.37.190',           //数据库域名
                    'name'      => 'phalapi_test',                  //数据库名字
                    'user'      => 'root',                  //数据库用户名
                    'password'  => '123456',                //数据库密码
                    'port'      => '3306',                  //数据库端口
                ),
            ),

            /**
             * custom table map
             */
            'tables' => array(
                '__default__' => array(
                    'prefix' => 'tpl_',
                    'key' => 'id',
                    'map' => array(
                        array('db' => 'DB_A'),
                    ),
                ),
                'demo' => array(
                    'prefix' => 'tpl_',
                    'key' => 'id',
                    'map' => array(
                        array('db' => 'DB_A'),
                        array('start' => 0, 'end' => 2, 'db' => 'DB_A'),
                        array('start' => 3, 'end' => 5, 'db' => 'DB_DEMO'),
                    ),
                ),
            ),
        );

        $this->coreDBNotORM = new PhalApi_DB_NotORM($config);
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
     * @expectedException PhalApi_Exception
     */
    public function testNoMap()
    {
        $notorm = new PhalApi_DB_NotORM(array());
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
