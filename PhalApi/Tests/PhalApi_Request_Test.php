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

    public function testService() {
        $requests = new PhalApi_Request(array('service' => 'Demo.Go'));

        $this->assertEquals('Demo.Go',  $requests->getService());
        $this->assertEquals('Demo',     $requests->getServiceApi());
        $this->assertEquals('Go',       $requests->getServiceAction());
    }

    public function testServiceDefault() {
        $requests = new PhalApi_Request();

        $this->assertEquals('Default.Index',    $requests->getService());
        $this->assertEquals('Default',          $requests->getServiceApi());
        $this->assertEquals('Index',            $requests->getServiceAction());
    }

    public function testServiceEmpty() {
        $requests = new PhalApi_Request(array('service' => ''));

        $this->assertSame('', $requests->getService());
        $this->assertSame('', $requests->getServiceApi());
        $this->assertSame(NULL, $requests->getServiceAction());
    }

    public function testServiceWithShortName() {
        $requests = new PhalApi_Request(array('s' => 'Demo.Go'));

        $this->assertEquals('Demo.Go',    $requests->getService());
    }

    public function testServiceWithFullNameFirst() {
        $requests = new PhalApi_Request(array('s' => 'DemoShort.GoShort', 'service' => 'Demo.Go'));

        $this->assertEquals('Demo.Go',    $requests->getService());
    }

    public function testSource()
    {
        $_POST['pp'] = 'p_data';
        $_GET['gg'] = 'g_data';
        $_COOKIE['cc'] = 'c_data';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        $_SERVER['ss'] = 's_data';
        $_REQUEST['rr'] = 'r_data';
        $data = array('dd' => 'd_data');

        $data = array_merge($data, $_POST, $_GET, $_COOKIE, $_SERVER, $_REQUEST);
        $requests = new PhalApi_Request($data);

        $postRs = $requests->getByRule(array('name' => 'pp', 'source' => 'post'));
        $this->assertEquals('p_data', $postRs);

        $getRs = $requests->getByRule(array('name' => 'gg', 'source' => 'get'));
        $this->assertEquals('g_data', $getRs);

        $cookieRs = $requests->getByRule(array('name' => 'cc', 'source' => 'cookie'));
        $this->assertEquals('c_data', $cookieRs);

        $headerRs = $requests->getByRule(array('name' => 'Accept-Charset', 'source' => 'header'));
        //$this->assertEquals('utf-8', $headerRs);

        $serverRs = $requests->getByRule(array('name' => 'ss', 'source' => 'server'));
        $this->assertEquals('s_data', $serverRs);

        $requestRs = $requests->getByRule(array('name' => 'rr', 'source' => 'request'));
        $this->assertEquals('r_data', $requestRs);

        $dataRs = $requests->getByRule(array('name' => 'dd'));
        $this->assertEquals('d_data', $dataRs);

        unset($_POST['pp'], $_GET['gg'], $_COOKIE['cc'], $_SERVER['HTTP_ACCEPT_CHARSET'], $_SERVER['ss'], $_REQUEST['rr']);
    }

    /**
     * @ expectedException PhalApi_Exception_InternalServerError
     */
    public function testUnkonwSource()
    {
        $requests = new PhalApi_Request(array());
        $notFoundRs = $requests->getByRule(array('name' => 'dd', 'source' => 'no_this_source'));
    }


    public function testGetSource()
    {
        $requests = new PhalApi_Request_TestMock(array());
        foreach (array('post', 'cookie', 'get', 'request', 'server', 'header') as $source) {
            $rs = $requests->getDataBySource($source);

            $this->assertTrue(is_array($rs));
        }
    }
}

class PhalApi_Request_TestMock extends PhalApi_Request {
    public function &getDataBySource($source) {
        return parent::getDataBySource($source);
    }
}

