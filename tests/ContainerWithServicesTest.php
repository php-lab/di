<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

use PhpLab\Di\Fake\{Component, Service};

class ContainerWithServicesTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->container->set('component', function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        });
    }

    public function testShouldGetPresettedComponent()
    {
        $service = $this->container->get('component');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Component', $service);
    }

    public function testShouldThrowExceptionIfServiceDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->container->get('commonService');
    }

    public function testShouldAssertWhatServiceDefinitionNotExists()
    {
        $result = $this->container->has('commonService');
        $this->assertFalse($result);
    }

    public function testShouldAssertWhatServiceDefinitionExists()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $result = $this->container->has('commonService');
        $this->assertTrue($result);
    }

    public function testShouldGetService()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $service = $this->container->get('commonService');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Service', $service);
    }

    public function testShouldGetSameService()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $instance1 = $this->container->get('commonService');
        $instance2 = $this->container->get('commonService');
        $this->assertSame($instance1, $instance2);
    }


    public function testShouldCallMethodOfComponentFromService()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $service = $this->container->get('commonService');
        $result = $service->getComponent()->getResult('value');
        $this->assertEquals('#value', $result);
    }

    public function testShouldDefineServiceWithOptionalArgument()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'), 'xml');
        });
        $service = $this->container->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldDefineServiceWithSetterInjection()
    {
        $this->container->set('commonService', function (Container $container) {
            $service = new Service($container->get('component'));
            $service->setFormat('xml');

            return $service;
        });
        $service = $this->container->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldGetServiceAfterChangeDefinition()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'), 'xml');
        });
        $service = $this->container->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingService()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $service = $this->container->get('commonService');
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'), 'xml');
        });
    }

    public function testShouldThrowExceptionIfExtendServiceDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->container->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
    }

    public function testShouldExtendServiseDefinition()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $this->container->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
        $service = $this->container->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfExtendDefinitionAfterGettingService()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $service = $this->container->get('commonService');
        $this->container->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
    }

    public function testShouldRemoveServiceDefinition()
    {
        $this->container->set('commonService', function (Container $container) {
            return new Service($container->get('component'));
        });
        $this->container->remove('commonService');
        $result = $this->container->has('commonService');
        $this->assertFalse($result);
    }
}
