<?php
namespace PhalApi\Tests;

use PhalApi\DependenceInjection;

/**
 * PhpUnderControl_PhalApiDI_Test
 *
 * 针对 ../PhalApi/DI.php DependenceInjection 类的PHPUnit单元测试
 *
 * @author: dogstar 20170708
 */

class PhpUnderControl_PhalApiDI_Test extends \PHPUnit_Framework_TestCase
{
    public $di;

    protected function setUp()
    {
        parent::setUp();

        $this->di = new DependenceInjection();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testOne
     */ 
    public function testOne()
    {
        $rs = \PhalApi\DI();
    }

    public function testSetterAndGetter()
    {
        $this->di->set('name', 'dogstar');
        $this->assertEquals($this->di->get('name'), 'dogstar');

        $arr = array(1, 5, 7);
        $this->di->set('nameArr', $arr);
        $this->assertEquals($this->di->get('nameArr'), $arr);
    }

    public function testMagicFunction()
    {
        $this->di->setName('dogstar');
        $this->assertEquals($this->di->getName(), 'dogstar');

        $this->assertEquals($this->di->getNameDefault('2013'), '2013');

        $this->assertEquals($this->di->getNameNull(), null);

        $this->di->setNameSetNull();
        $this->assertEquals($this->di->getNameSetNull(), null);
    }

    public function testClassSettterAndGetter()
    {
        $this->di->name2 = 'dogstar';
        $this->assertEquals($this->di->name2, 'dogstar');

        $this->di->nameAgain = 'dogstarAgain';
        $this->assertEquals($this->di->nameAgain, 'dogstarAgain');

        $this->assertNull($this->di->nameNull);

    }

    public function testMixed()
    {
        $this->di->name1 = 'dogstar1';
        $this->assertEquals($this->di->name1, 'dogstar1');
        $this->assertEquals($this->di->getName1('2013'), 'dogstar1');
        $this->assertEquals($this->di->name1, 'dogstar1');

        $this->di->setName1('dogstar2');
        $this->assertEquals($this->di->name1, 'dogstar2');
        $this->assertEquals($this->di->getName1('2013'), 'dogstar2');
        $this->assertEquals($this->di->name1, 'dogstar2');

        $this->di->set('name1', 'dogstar3');
        $this->assertEquals($this->di->name1, 'dogstar3');
        $this->assertEquals($this->di->getName1('2013'), 'dogstar3');
        $this->assertEquals($this->di->name1, 'dogstar3');

    }

    public function testAnonymousFunction()
    {
        $this->di->set('name', function(){
            return new DIDemo(2014);   
        });

        $this->assertEquals($this->di->name->mark, 2014);

        $mark = 2015;
        $this->di->set('name1', function() use ($mark){
            return new DIDemo($mark);
        });
        $this->assertEquals($this->di->name1->mark, $mark);

        $this->di->name3 = function(){
            return new DIDemo(2015);
        };
        $this->assertEquals($this->di->getName3()->mark, 2015);
    }

    public function testLazyLoadClass()
    {
        $this->di->setName('\PhalApi\Tests\DIDemo2');
        $this->assertEquals($this->di->name instanceof DIDemo2, true);
        $this->assertEquals($this->di->name->number, 3);
        $this->assertEquals($this->di->name->number, 3);
        $this->di->name->number = 9;
        $this->assertEquals($this->di->name->number, 9);
        $this->assertEquals($this->di->getName()->number, 9);
    }

    public function testArrayIndex()
    {
        $this->di['name'] = 'dogstar';
        $this->assertEquals($this->di->name, 'dogstar');

        $this->di[2014] = 'horse';
        $this->assertEquals($this->di->get2014(), 'horse');
        $this->assertEquals($this->di[2014], 'horse');
        $this->assertEquals($this->di->get(2014), 'horse');
    }

    /**
     * hope nobody use DI as bellow
     */
    public function testException()
    {
        try {
            $this->di[array(1)] = 1;
            $this->fail('no way');
        } catch (\Exception $ex) {
        }

        try {
            $this->di->set(array(1), array(1));
            $this->fail('no way');
        } catch (\Exception $ex) {
        }

        try {
            $this->di->get(array(1), array(1));
            $this->fail('no way');
        } catch (\Exception $ex) {
        }
    }

    public function testSetAgainAndAgain()
    {
        $this->di['name'] = function () {
            return 'dogstar';
        };
        $this->assertEquals('dogstar', $this->di['name']);

        $this->di['name'] = 'dogstar2';
        $this->assertEquals('dogstar2', $this->di['name']);

        $this->di['name'] = function () {
            return 'dogstar3';
        };
        $this->assertEquals('dogstar3', $this->di['name']);

        $this->di['name'] = 'dogstar4';
        $this->assertEquals('dogstar4', $this->di['name']);

        $this->di['name'] = 'dogstar5';
        $this->assertEquals('dogstar5', $this->di['name']);

    }

    public function testSetAgainAndAgainByProperty()
    {
        $this->di->name = 'name';
        $this->assertSame('name', $this->di->name);

        $this->di->name = 'name2';
        $this->assertSame('name2', $this->di->name);

        $this->di->name = array('name3');
        $this->assertSame(array('name3'), $this->di->name);
    }

    public function testSetAgainAndAgainBySetter()
    {
        $this->di->set('name', 'value');
        $this->assertSame('value', $this->di->name);

        $this->di->set('name', 'value2');
        $this->assertSame('value2', $this->di->name);
    }

    public function testSetAgainAndAgainByMagic()
    {
        $this->di->setName('value');
        $this->assertSame('value', $this->di->name);

        $this->di->setName('value2');
        $this->assertSame('value2', $this->di->name);
    }

    public function testGetAgainAndAgain()
    {
        $times = 0;

        $this->di['name'] = function () use (&$times) {
            $times ++;
            return 'dogstar';
        };

        $this->assertEquals('dogstar', $this->di['name']);
        $this->assertEquals('dogstar', $this->di['name']);
        $this->assertEquals('dogstar', $this->di['name']);
        $this->assertEquals('dogstar', $this->di['name']);

        $this->assertEquals(1, $times);
    }

    public function testNumericKey()
    {
        $this->di[0] = 0;
        $this->di[1] = 10;
        $this->di[2] = 20;

        $this->assertSame(0, $this->di[0]);
        $this->assertSame(0, $this->di->get(0));
        $this->assertSame(0, $this->di->get0());

        $this->assertSame(10, $this->di[1]);
        $this->assertSame(20, $this->di->get(2));
        $this->assertSame(20, $this->di->get2());
    }

    public function testMultiLevel()
    {
        $this->di->dogstar = new DependenceInjection();
        $this->di->dogstar->name = "dogstar";
        $this->di->dogstar->age = "?";
        $this->di->dogstar->id = 1;

        $this->assertSame('dogstar', $this->di->dogstar->name);
        $this->assertSame('?', $this->di->dogstar->age);
        $this->assertSame(1, $this->di->dogstar->id = 1);
    }

    public function testWithClassName()
    {
        $this->di->key = '\\PhalApi\\DependenceInjection';
        $this->assertInstanceOf('\\PhalApi\\DependenceInjection', $this->di->key);
    }

    public function testArrayUsage()
    {
        $this->di[0] = 0;
        $this->di[1] = 1;
        $this->di[2] = 2;

        foreach ($this->di as $key => $value) {
            $this->assertSame($key, $value);
        }
    }

    /**
     * @expectedException Exception
     */
    public function testIllegalMethod()
    {
        $this->di->doSomeThingWrong();
    }

    public function testMultiSet()
    {
        $this->di->set('key1', 'value1')
            ->set('key2', 'value2')
            ->set('key2', 'value2')
            ->set('key3', 'value3');

        $this->di->setKey4('value4')
            ->setKey5('value5')
            ->setkey6('value6');

        $this->assertSame('value2', $this->di->key2);
        $this->assertSame('value6', $this->di['key6']);
    }

    public function testOneWithInstanceNull()
    {
        $oldInstance = DependenceInjectionMock::getInstance();

        DependenceInjectionMock::setInstance(null);
        $newDI = DependenceInjection::one();

        if (!isset($newDI['tmp'])) {
            $newDI['tmp'] = '2015';

            $this->assertEquals('2015', $newDI['tmp']);
            unset($newDI['tmp']);
        }

        $this->assertEquals(null, $newDI['tmp']);

        DependenceInjectionMock::setInstance($oldInstance);
    }
}

class DIDIDemo
{
    public $hasConstruct = false;
    public $hasInitialized = false;

    public $mark = null;

    public function __construct($mark)
    {
        //echo "DIDemo::__construct()\n";

        $this->mark = $mark;
    }

    public function onConstruct()
    {
        $this->hasConstruct = true;
        //echo "DIDemo::onConstruct()\n";
    }

    public function onInitialize()
    {  
        $this->hasInitialize = true;
        //echo "DIDemo:: onInitialize()\n";
    }
}


class DIDemo
{
    public $hasConstruct = false;
    public $hasInitialized = false;

    public $mark = null;

    public function __construct($mark)
    {
        //echo "DIDemo::__construct()\n";

        $this->mark = $mark;
    }

    public function onConstruct()
    {
        $this->hasConstruct = true;
        //echo "DIDemo::onConstruct()\n";
    }

    public function onInitialize()
    {  
        $this->hasInitialize = true;
        //echo "DIDemo:: onInitialize()\n";
    }
}

class DIDemo2 extends DIDemo
{
    public $number = 1;

    public function __construct()
    {
        //echo "DIDemo2::__construct()\n";   
    }

    public function onConstruct()
    {
        //echo "DIDemo2::onConstruct()\n";
        $this->number = 2;
        parent::onConstruct();
    }

    public function onInitialize()
    {
        //echo "DIDemo2::onInitialize()\n";
        $this->number = 3;
        parent::onInitialize();
    }

    public function onInit()
    {
        $this->onInitialize();
    }
}

class DependenceInjectionMock extends DependenceInjection {

    public static function getInstance(){
        return DependenceInjection::$instance;
    }

    public static function setInstance($instance) {
        DependenceInjection::$instance = $instance;
    }
}
