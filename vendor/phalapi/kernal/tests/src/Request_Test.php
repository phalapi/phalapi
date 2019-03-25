<?php
namespace PhalApi\Tests;

use PhalApi\Request;

/**
 * PhpUnderControl_PhalApiRequest_Test
 *
 * 针对 ../PhalApi/Request.php Request 类的PHPUnit单元测试
 *
 * @author: dogstar 20141004
 */

class PhpUnderControl_PhalApiRequest_Test extends \PHPUnit_Framework_TestCase
{
    public $request;

    protected function setUp()
    {
        parent::setUp();

        $data = array('year' => '2014', 'version' => '1.0.0');
        $this->request = new Request($data);
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

        $rs = $this->request->get($key, $default);

        $this->assertSame('2014', $rs);
    }

    /**
     * @group testGetByRule
     */ 
    public function testGetByRule()
    {
        $rule = array('name' => 'version', 'type' => 'string', 'default' => '0.0.0');

        $rs = $this->request->getByRule($rule);

        $this->assertEquals('1.0.0', $rs);
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testGetByComplexRule()
    {
        $rule = array('name' => 'year', 'type' => 'int', 'min' => '2000', 'max' => '2013');

        $rs = $this->request->getByRule($rule);

        $this->assertSame(2013, $rs);
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 我需要一个正经的整数
     */
    public function testGetByComplexRuleWithMessage()
    {
        $rule = array('name' => 'year', 'type' => 'int', 'min' => '2000', 'max' => '2013', 'message' => '我需要一个正经的整数');

        $rs = $this->request->getByRule($rule);

        $this->assertSame(2013, $rs);
    }

    /**
     * @group testGetAll
     */ 
    public function testGetAll()
    {
        $rs = $this->request->getAll();
        $this->assertEquals(array('year' => '2014', 'version' => '1.0.0'), $rs);
    }

    public function testConstructWithREQUEST()
    {
        $request = new Request();

        $this->assertTrue(true);
    }

    /**
     * @expectedException \PhalApi\Exception\InternalServerErrorException
     */
    public function testIllegalRule()
    {
        $this->request->getByRule(array());
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     */
    public function testGetRequireVal()
    {
        $this->request->getByRule(array('name' => 'requireVal', 'require' => true));
    }

    /**
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 必须
     */
    public function testGetRequireValWithMessage()
    {
        $this->request->getByRule(array('name' => 'requireVal', 'require' => true, 'message' => '必须哦'));
    }

    public function testGetHeader()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/text';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        //$_SERVER['PHP_AUTH_DIGEST'] = 'xxx';

        $request = new Request();
        $this->assertEquals('application/text', $request->getHeader('Accept'));
        $this->assertEquals('utf-8', $request->getHeader('Accept-Charset'));
        //$this->assertEquals('xxx', $request->getHeader('AUTHORIZATION'));

        $this->assertEquals('123', $request->getHeader('no-this-key', '123'));
        $this->assertSame(NULL, $request->getHeader('no-this-key'));

        unset($_SERVER['HTTP_ACCEPT']);
        unset($_SERVER['HTTP_ACCEPT_CHARSET']);
        unset($_SERVER['PHP_AUTH_DIGEST']);
    }

    // 兼容多种拼写方式
    public function testGetHeaderMoreKindly()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';

        $request = new Request();
        $this->assertEquals('PHPUnit', $request->getHeader('HTTP_USER_AGENT'));
        $this->assertEquals('PHPUnit', $request->getHeader('User-Agent'));

        unset($_SERVER['HTTP_USER_AGENG']);
    }

    public function testService() {
        $requests = new Request(array('service' => 'Demo.Go'));

        $this->assertEquals('App.Demo.Go',  $requests->getService());
        $this->assertEquals('Demo',     $requests->getServiceApi());
        $this->assertEquals('Go',       $requests->getServiceAction());
    }

    public function testServiceDefault() {
        $requests = new Request();

        $this->assertEquals('App.Site.Index',   $requests->getService());
        $this->assertEquals('App',              $requests->getNamespace());
        $this->assertEquals('Site',             $requests->getServiceApi());
        $this->assertEquals('Index',            $requests->getServiceAction());
    }

    public function testServiceEmpty() {
        $requests = new Request(array('service' => ''));

        $this->assertSame('', $requests->getService());
        $this->assertSame(NULL, $requests->getServiceApi());
        $this->assertSame(NULL, $requests->getServiceAction());
    }

    public function testServiceWithShortName() {
        $requests = new Request(array('s' => 'Demo.Go'));

        $this->assertEquals('App.Demo.Go',  $requests->getService());
        $this->assertEquals('Demo',     $requests->getServiceApi());
        $this->assertEquals('Go',       $requests->getServiceAction());
    }

    public function testServiceWithFullNameFirst() {
        $requests = new Request(array('s' => 'DemoShort.GoShort', 'service' => 'Demo.Go'));

        $this->assertEquals('App.Demo.Go',  $requests->getService());
        $this->assertEquals('Demo',     $requests->getServiceApi());
        $this->assertEquals('Go',       $requests->getServiceAction());
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

        $requests = new Request($data);

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
     * @expectedException \PhalApi\Exception\InternalServerErrorException
     * @expectedExceptionMessage no_this_source
     */
    public function testUnkonwSource()
    {
        $requests = new RequestTestMock(array());
        $requests->getDataBySource('no_this_source');
    }

    public function testGetSource()
    {
        $requests = new RequestTestMock(array());
        foreach (array('post', 'cookie', 'get', 'request', 'server', 'header') as $source) {
            $rs = $requests->getDataBySource($source);

            $this->assertTrue(is_array($rs));
        }
    }
}

class RequestTestMock extends Request {
    public function &getDataBySource($source) {
        return parent::getDataBySource($source);
    }
}
