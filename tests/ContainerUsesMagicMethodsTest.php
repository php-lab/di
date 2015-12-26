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
    protected $app;

    public function setUp()
    {
        $this->app = new Container();
        $this->app->component = function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        };
    }

    public function testShouldGetPresettedComponentUsingMagic()
    {
        $service = $this->app->component;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Component', $service);
    }

    public function testShouldThrowExceptionIfServiceDefinitionNotFoundUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->app->commonService;
    }

    public function testShouldGetServiceUsingMagic()
    {
        $this->app->commonService = function (Container $di) {
            return new Service($di->component);
        };
        $service = $this->app->commonService;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Service', $service);
    }

    public function testShouldGetServiceAfterChangeDefinitionUsingMagic()
    {
        $this->app->commonService = function (Container $di) {
            return new Service($di->component);
        };
        $this->app->commonService = function (Container $di) {
            return new Service($di->component, 'xml');
        };
        $service = $this->app->commonService;
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingServiceUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->commonService = function (Container $di) {
            return new Service($di->component);
        };
        $service = $this->app->commonService;
        $this->app->commonService = function (Container $di) {
            return new Service($di->component, 'xml');
        };
    }

    public function testShouldGetObjectUsingMagic()
    {
        $this->app->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance = $this->app->_newObjectEveryTime;
        $this->assertInstanceOf('\PhpLab\Di\Fake\Object', $instance);
    }

    public function testShouldGetNewInstanceOfObjectUsingMagic()
    {
        $this->app->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance1 = $this->app->_newObjectEveryTime;
        $instance2 = $this->app->_newObjectEveryTime;
        $this->assertNotSame($instance1, $instance2);
    }

    public function testShouldGetObjectAfterChangeDefinitionUsingMagic()
    {
        $this->app->_newObjectEveryTime = function () {
            return new Object();
        };
        $this->app->_newObjectEveryTime = function () {
            return new Object('value');
        };
        $instance = $this->app->_newObjectEveryTime;
        $this->assertEquals('value', $instance->getValue());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingObjectUsingMagic()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->_newObjectEveryTime = function () {
            return new Object();
        };
        $instance = $this->app->_newObjectEveryTime;
        $this->app->_newObjectEveryTime = function () {
            return new Object('value');
        };
    }
}
