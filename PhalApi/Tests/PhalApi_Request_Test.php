<?php
/**
 * PhpUnderControl_PhalApiRequest_Test
 *
 * 针对 ../PhalApi/Request.php PhalApi_Request 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

require_once dirname(__FILE__) . '/test_env.php';

if (!class_exists('PhalApi_Request')) {
    require dirname(__FILE__) . '/../PhalApi/Request.php';
}

class PhpUnderControl_PhalApiRequest_Test extends PHPUnit_Framework_TestCase
{
    public $coreRequest;

    protected function setUp()
    {
        parent::setUp();

        $data = array('year' => '2014', 'version' => '1.0.0');
        $this->coreRequest = new PhalApi_Request($data);
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
     * @expectedException PhalApi_Exception_BadRequest
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

    public function testConstructWithREQUEST()
    {
        $request = new PhalApi_Request();
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testIllegalRule()
    {
        $this->coreRequest->getByRule(array());
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testGetRequireVal()
    {
        $this->coreRequest->getByRule(array('name' => 'requireVal', 'require' => true));
    }

    public function testGetHeader()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/text';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        //$_SERVER['PHP_AUTH_DIGEST'] = 'xxx';

        $request = new PhalApi_Request();
        $this->assertEquals('application/text', $request->getHeader('Accept'));
        $this->assertEquals('utf-8', $request->getHeader('Accept-Charset'));
        //$this->assertEquals('xxx', $request->getHeader('AUTHORIZATION'));

        $this->assertEquals('123', $request->getHeader('no-this-key', '123'));
        $this->assertSame(NULL, $request->getHeader('no-this-key'));

        unset($_SERVER['HTTP_ACCEPT']);
        unset($_SERVER['HTTP_ACCEPT_CHARSET']);
        unset($_SERVER['PHP_AUTH_DIGEST']);
    }
}
