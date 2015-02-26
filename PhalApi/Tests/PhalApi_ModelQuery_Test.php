<?php
/**
 * PhpUnderControl_PhalApiModelQuery_Test
 *
 * 针对 ../PhalApi/ModelQuery.php PhalApi_ModelQuery 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_ModelQuery')) {
    require dirname(__FILE__) . '/../PhalApi/ModelQuery.php';
}

class PhpUnderControl_PhalApiModelQuery_Test extends PHPUnit_Framework_TestCase
{
    public $phalApiModelQuery;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiModelQuery = new PhalApi_ModelQuery();
    }

    protected function tearDown()
    {
    }

    public function testMixed() 
    {
        $this->phalApiModelQuery->name = 'dogstar';

        $this->assertEquals('dogstar', $this->phalApiModelQuery->name);

        $this->assertNull($this->phalApiModelQuery->noThisKey);

        $this->assertTrue($this->phalApiModelQuery->readCache);
        $this->assertTrue($this->phalApiModelQuery->writeCache);
    }

    /**
     * @group testToArray
     */ 
    public function testToArray()
    {
        $rs = $this->phalApiModelQuery->toArray();

        $this->assertTrue(is_array($rs));

        $this->assertTrue($rs['readCache']);
        $this->assertTrue($rs['writeCache']);
    }

    public function testConstructFromToArray()
    {
        $query = new PhalApi_ModelQuery();
        $query->readCache = false;
        $query->name = 'phpunit';

        $query2 = new PhalApi_ModelQuery($query->toArray());

        $this->assertEquals('phpunit', $query2->name);

        $this->assertEquals($query->toArray(), $query2->toArray());
        $this->assertEquals($query, $query2);
    }
}
