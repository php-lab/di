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
    protected $app;

    public function setUp()
    {
        $this->app = new DevContainer();
        $this->app->set('component', function () {
            return new Component(function ($value) {
                return '#' . $value;
            });
        });
    }

    public function testShouldGetDevServiceInsteadProdService()
    {
        $this->app->set('commonService', function (Container $di) {
            return new Service($di->get('component'));
        });
        // With 'Dev' suffix
        $this->app->set('commonServiceDev', function (Container $di) {
            return new DevService($di->get('component'));
        });
        $instance = $this->app->get('commonService');
        $this->assertInstanceOf('\PhpLab\Di\Fake\DevService', $instance);
    }

    public function testShouldGetDevParameterInsteadProdParameter()
    {
        $this->app['param.test_value'] = 'value';
        // With '_dev' suffix
        $this->app['param.test_value_dev'] = 'dev value';
        $param = $this->app['param.test_value'];
        $this->assertEquals('dev value', $param);
    }
}
