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
    protected $app;

    public function setUp()
    {
        $this->app = new Container();
        $this->app->set('component', function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        });
    }

    public function testShouldGetPresettedComponent()
    {
        $service = $this->app->get('component');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Component', $service);
    }

    public function testShouldThrowExceptionIfServiceDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->app->get('commonService');
    }

    public function testShouldAssertWhatServiceDefinitionNotExists()
    {
        $result = $this->app->has('commonService');
        $this->assertFalse($result);
    }

    public function testShouldAssertWhatServiceDefinitionExists()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $result = $this->app->has('commonService');
        $this->assertTrue($result);
    }

    public function testShouldGetService()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $service = $this->app->get('commonService');
        $this->assertInstanceOf('\PhpLab\Di\Fake\Service', $service);
    }

    public function testShouldGetSameService()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $instance1 = $this->app->get('commonService');
        $instance2 = $this->app->get('commonService');
        $this->assertSame($instance1, $instance2);
    }


    public function testShouldCallMethodOfComponentFromService()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $service = $this->app->get('commonService');
        $result = $service->getComponent()->getResult('value');
        $this->assertEquals('#value', $result);
    }

    public function testShouldDefineServiceWithOptionalArgument()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'), 'xml');
        });
        $service = $this->app->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldDefineServiceWithSetterInjection()
    {
        $this->app->set('commonService', function (Container $di) {
            $service = new Service($di->get('component'));
            $service->setFormat('xml');

            return $service;
        });
        $service = $this->app->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldGetServiceAfterChangeDefinition()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'), 'xml');
        });
        $service = $this->app->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfChangeDefinitionAfterGettingService()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $service = $this->app->get('commonService');
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'), 'xml');
        });
    }

    public function testShouldThrowExceptionIfExtendServiceDefinitionNotFound()
    {
        $this->setExpectedException('\PhpLab\Di\NotFoundException');
        $this->app->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
    }

    public function testShouldExtendServiseDefinition()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $this->app->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
        $service = $this->app->get('commonService');
        $this->assertEquals('xml', $service->getFormat());
    }

    public function testShouldThrowExceptionIfExtendDefinitionAfterGettingService()
    {
        $this->setExpectedException('\PhpLab\Di\FrozenException');
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $service = $this->app->get('commonService');
        $this->app->extend('commonService', function ($service) {
            $service->setFormat('xml');

            return $service;
        });
    }

    public function testShouldRemoveServiceDefinition()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        $this->app->remove('commonService');
        $result = $this->app->has('commonService');
        $this->assertFalse($result);
    }
}
