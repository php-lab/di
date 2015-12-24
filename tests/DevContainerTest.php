<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di;

use PhpLab\Di\Fake\{Component, Service, DevService};

class DevContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new DevContainer();
        $this->container->set('component', function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        });
    }

    public function testShouldGetDevServiceInsteadProdService()
    {
        $this->container->set('commonService', function (DevContainer $container) {
            return new Service($container->get('component'));
        });
        // With 'Dev' suffix
        $this->container->set('commonServiceDev', function (DevContainer $container) {
            return new DevService($container->get('component'));
        });
        $instance = $this->container->get('commonService');
        $this->assertInstanceOf('\PhpLab\Di\Fake\DevService', $instance);
    }

    public function testShouldGetDevParameterInsteadProdParameter()
    {
        $this->container['param.test_value'] = 'value';
        // With '_dev' suffix
        $this->container['param.test_value_dev'] = 'dev value';
        $param = $this->container['param.test_value'];
        $this->assertEquals('dev value', $param);
    }
}
