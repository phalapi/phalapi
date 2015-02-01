<?php
/**
 * PhpUnderControl_CoreRequest_Test
 *
 * 针对 ../Core/Request.php Core_Request 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('Core_Request')) {
    require dirname(__FILE__) . '/../Core/Request.php';
}

class PhpUnderControl_CoreRequest_Test extends PHPUnit_Framework_TestCase
{
    public $coreRequest;

    protected function setUp()
    {
        parent::setUp();

        $data = array('year' => '2014', 'version' => '1.0.0');
        $this->coreRequest = new Core_Request($data);
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'year';
        $default = '2015';

        $rs = $this->coreRequest->get($key, $default);

        $this->assertSame('2014', $rs);
    }

    /**
     * @group testGetByRule
     */ 
    public function testGetByRule()
    {
        $rule = array('name' => 'version', 'type' => 'string', 'default' => '0.0.0');

        $rs = $this->coreRequest->getByRule($rule);

        $this->assertEquals('1.0.0', $rs);
    }

    /**
     * @expectedException Core_Exception_BadRequest
     */
    public function testGetByComplexRule()
    {
        $rule = array('name' => 'year', 'type' => 'int', 'min' => '2000', 'max' => '2013');

        $rs = $this->coreRequest->getByRule($rule);

        $this->assertSame(2013, $rs);
    }

    /**
     * @group testGetAll
     */ 
    public function testGetAll()
    {
        $rs = $this->coreRequest->getAll();
        $this->assertEquals(array('year' => '2014', 'version' => '1.0.0'), $rs);
    }

}
