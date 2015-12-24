<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

use PhpLab\Di\Fake\{Component, Service, Object};

class ContainerUsesMagicMethodsTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->container->component = function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        };
    }

    public function testShouldGetPresettedComponentUsingMagic()
    {
        $service = $this->container->component;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Component', $service);
    }

    public function testShouldThrowExceptionIfServiceDefinitionNotFoundUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->container->commonService;
    }

    public function testShouldGetServiceUsingMagic()
    {
        $this->container->commonService = function (Container $container) {
            return new Service($container->component);
        };
        $service = $this->container->commonService;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Service', $service);
    }

    public function testShouldGetServiceAfterChangeDefinitionUsingMagic()
    {
        $this->container->commonService = function (Container $container) {
            return new Service($container->component);
        };
        $this->container->commonService = function (Container $container) {
            return new Service($container->component, 'xml');
        };
        $service = $this->container->commonService;
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingServiceUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->commonService = function (Container $container) {
            return new Service($container->component);
        };
        $service = $this->container->commonService;
        $this->container->commonService = function (Container $container) {
            return new Service($container->component, 'xml');
        };
    }

    public function testShouldGetObjectUsingMagic()
    {
        $this->container->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance = $this->container->_newObjectEveryTime;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Object', $instance);
    }

    public function testShouldGetNewInstanceOfObjectUsingMagic()
    {
        $this->container->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance1 = $this->container->_newObjectEveryTime;
        $instance2 = $this->container->_newObjectEveryTime;
        $this->assertNotSame($instance1, $instance2);
    }

    public function testShouldGetObjectAfterChangeDefinitionUsingMagic()
    {
        $this->container->_newObjectEveryTime = function () {
            return new Object();
        };
        $this->container->_newObjectEveryTime = function () {
            return new Object('value');
        };
        $instance = $this->container->_newObjectEveryTime;
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingObjectUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance = $this->container->_newObjectEveryTime;
        $this->container->_newObjectEveryTime = function () {
            return new Object('value');
        };
    }
}
