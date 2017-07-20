<?php
namespace PhalApi\Tests;

use PhalApi\Model\Query;

/**
 * PhpUnderControl_PhalApiModelQuery_Test
 *
 * 针对 ../PhalApi/ModelQuery.php Query 类的PHPUnit单元测试
 *
 * @author: dogstar 20150226
 */

class PhpUnderControl_PhalApiModelQuery_Test extends \PHPUnit_Framework_TestCase
{
    public $query;

    protected function setUp()
    {
        parent::setUp();

        \PhalApi\DI()->debug = false;

        $this->query = new Query();
    }

    protected function tearDown()
    {
        \PhalApi\DI()->debug = true;
    }

    public function testMixed() 
    {
        $this->query->name = 'dogstar';

        $this->assertEquals('dogstar', $this->query->name);

        $this->assertNull($this->query->noThisKey);

        $this->assertTrue($this->query->readCache);
        $this->assertTrue($this->query->writeCache);
    }

    /**
     * @group testToArray
     */ 
    public function testToArray()
    {
        $rs = $this->query->toArray();

        $this->assertTrue(is_array($rs));

        $this->assertTrue($rs['readCache']);
        $this->assertTrue($rs['writeCache']);
    }

    public function testConstructFromToArray()
    {
        $query = new Query();
        $query->readCache = false;
        $query->name = 'phpunit';

        $query2 = new Query($query->toArray());

        $this->assertEquals('phpunit', $query2->name);

        $this->assertEquals($query->toArray(), $query2->toArray());
        $this->assertEquals($query, $query2);
    }
}
