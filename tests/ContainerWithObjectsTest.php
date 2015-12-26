<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

use PhpLab\Di\Fake\Object;

class ContainerWithObjectsTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Container();
    }

    public function testShouldAssertWhatObjectDefinitionExists()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $result = $this->app->has('_newObjectEveryTime');
        $this->assertTrue($result);
    }

    public function testShouldGetObject()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Object', $instance);
    }

    public function testShouldGetNewInstanceOfObject()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance1 = $this->app->get('_newObjectEveryTime');
        $instance2 = $this->app->get('_newObjectEveryTime');
        $this->assertNotSame($instance1, $instance2);
    }

    public function testShouldDefineObjectWithOptionalArgument()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldDefineObjectWithSetterInjection()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            $object = new Object();
            $object->setValue('value');

            return $object;
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldGetObjectAfterChangeDefinition()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingObject()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
    }

    public function testShouldThrowExceptionIfExtendObjectDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->app->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
    }

    public function testShouldExtendObjectDefinition()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->app->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfExtendDefinitionAfterGettingObject()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->app->get('_newObjectEveryTime');
        $this->app->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
    }

    public function testShouldRemoveObjectDefinition()
    {
        $this->app->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->app->remove('_newObjectEveryTime');
        $result = $this->app->has('_newObjectEveryTime');
        $this->assertFalse($result);
    }
}
