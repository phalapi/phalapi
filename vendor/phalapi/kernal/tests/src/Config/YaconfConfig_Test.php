<?php
namespace PhalApi\Tests;

use PhalApi\Config\YaconfConfig;

include_once dirname(__FILE__) . '/yaconf.php';

/**
 * PhpUnderControl_PhalApiConfigYaconf_Test
 *
 * 针对 ../../PhalApi/Config/Yaconf.php PhalApi_Config_Yaconf 类的PHPUnit单元测试
 *
 * @author: dogstar 20151109
 */

class PhpUnderControl_PhalApiConfigYaconf_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiConfigYaconf;

    protected function setUp()
    {
        parent::setUp();

        /**
         * vim ./test.ini
         *
         * name="PhalApi"
         * version="1.3.1"
         */

        $this->phalApiConfigYaconf = new YaconfConfig();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testGet
     */ 
    public function testGet()
    {
        $key = 'test.name';
        $default = NULL;

        $rs = $this->phalApiConfigYaconf->get($key, $default);

        $this->assertEquals('PhalApi', $rs);
    }

    /**
     * @group testHas
     */ 
    public function testHas()
    {
        $key = 'test.version';

        $rs = $this->phalApiConfigYaconf->has($key);

        $this->assertTrue($rs);
    }

}
