<?php

require_once dirname(__FILE__) . '/../../bootstrap.php';

/**
 * PhpUnderControl_PhalApi\Task\Progress\Trigger\CommonTrigger_Test
 *
 * 针对 ../src/Progress/Trigger/CommonTrigger.php PhalApi\Task\Progress\Trigger\CommonTrigger 类的PHPUnit单元测试
 *
 * @author: dogstar 20170720
 */

class PhpUnderControl_PhalApiTaskProgressTriggerCommonTrigger_Test extends \PHPUnit_Framework_TestCase
{
    public $phalApiTaskProgressTriggerCommonTrigger;

    protected function setUp()
    {
        parent::setUp();

        $this->phalApiTaskProgressTriggerCommonTrigger = new PhalApi\Task\Progress\Trigger\CommonTrigger();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(DI()->tracer->getSqls());
    }


    /**
     * @group testFire
     */ 
    public function testFire()
    {
        $params = 'Site.Index';

        $rs = $this->phalApiTaskProgressTriggerCommonTrigger->fire($params);
    }

}
