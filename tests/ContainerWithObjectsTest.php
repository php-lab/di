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
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testShouldAssertWhatObjectDefinitionExists()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $result = $this->container->has('_newObjectEveryTime');
        $this->assertTrue($result);
    }

    public function testShouldGetObject()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Object', $instance);
    }

    public function testShouldGetNewInstanceOfObject()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance1 = $this->container->get('_newObjectEveryTime');
        $instance2 = $this->container->get('_newObjectEveryTime');
        $this->assertNotSame($instance1, $instance2);
    }

    public function testShouldDefineObjectWithOptionalArgument()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldDefineObjectWithSetterInjection()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            $object = new Object();
            $object->setValue('value');

            return $object;
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldGetObjectAfterChangeDefinition()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingObject()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object('value');
        });
    }

    public function testShouldThrowExceptionIfExtendObjectDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->container->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
    }

    public function testShouldExtendObjectDefinition()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->container->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfExtendDefinitionAfterGettingObject()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $instance = $this->container->get('_newObjectEveryTime');
        $this->container->extend('_newObjectEveryTime', function ($object) {
            $object->setValue('value');

            return $object;
        });
    }

    public function testShouldRemoveObjectDefinition()
    {
        $this->container->setBuilder('_newObjectEveryTime', function () {
            return new Object();
        });
        $this->container->remove('_newObjectEveryTime');
        $result = $this->container->has('_newObjectEveryTime');
        $this->assertFalse($result);
    }
}
